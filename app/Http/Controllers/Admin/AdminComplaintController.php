<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Http\Requests\ResolveComplaintRequest;
use App\Http\Requests\RejectComplaintRequest;
use App\Services\ComplaintService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminComplaintController extends Controller implements HasMiddleware
{
    protected ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'role:admin',
        ];
    }

    // ponytail: view all complaints
    public function index()
    {
        $complaints = Complaint::with(['complainant', 'respondent', 'order'])->latest()->paginate(15);
        return view('admin.complaints.index', compact('complaints'));
    }

    // ponytail: view complaint details with chat messages
    public function show(Complaint $complaint)
    {
        $complaint->load(['complainant', 'respondent', 'order', 'messages.user']);
        return view('admin.complaints.show', compact('complaint'));
    }

    // ponytail: admin posts message in dispute discussion
    public function storeMessage(\Illuminate\Http\Request $request, Complaint $complaint)
    {
        $this->authorize('process', $complaint);

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $attachmentUrl = $request->file('attachment')->store('complaint_messages', 'public');
        }

        $complaint->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->input('message'),
            'attachment_url' => $attachmentUrl,
        ]);

        return redirect()->back()->with('success', 'Pesan Admin berhasil dikirim.');
    }

    // ponytail: mark complaint as being processed
    public function process(Complaint $complaint)
    {
        $this->authorize('process', $complaint);
        try {
            $this->complaintService->processComplaint(auth()->user(), $complaint, []);
            return redirect()->back()->with('success', 'Complaint is now under review.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: resolve complaint
    public function resolve(ResolveComplaintRequest $request, Complaint $complaint)
    {
        $this->authorize('process', $complaint);
        try {
            $this->complaintService->resolveComplaint(auth()->user(), $complaint, $request->input('resolution_note'));
            return redirect()->back()->with('success', 'Complaint resolved successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: reject complaint
    public function reject(RejectComplaintRequest $request, Complaint $complaint)
    {
        $this->authorize('process', $complaint);
        try {
            $this->complaintService->rejectComplaint(auth()->user(), $complaint, $request->input('resolution_note') ?? $request->input('reason') ?? '');
            return redirect()->back()->with('success', 'Complaint rejected successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function acceptAppeal(\Illuminate\Http\Request $request, Complaint $complaint, \App\Services\WalletService $walletService)
    {
        $this->authorize('process', $complaint);
        
        $complaint->update([
            'status' => Complaint::STATUS_REJECTED, // Reversing the decision so seller wins
            'resolution_note' => $complaint->resolution_note . "\n\n[BANDING DITERIMA] " . $request->input('appeal_resolution_note'),
        ]);

        $order = $complaint->order;
        if ($order && $order->order_status !== \App\Models\Order::STATUS_COMPLETED) {
            $order->update(['order_status' => \App\Models\Order::STATUS_COMPLETED]);
            $order->loadMissing(['items', 'payment', 'seller']);

            // Decrement listing stock
            foreach ($order->items as $item) {
                $listing = \App\Models\WasteListing::where('id', $item->listing_id)->lockForUpdate()->first();
                if ($listing && $listing->quantity >= $item->quantity) {
                    $listing->decrement('quantity', $item->quantity);
                    if ($listing->quantity <= 0) {
                        $listing->update(['availability_status' => \App\Models\WasteListing::AVAILABILITY_SOLD_OUT]);
                    }
                }
            }

            // Credit seller wallet
            $paymentMethod = $order->payment ? $order->payment->payment_method : null;
            if ($paymentMethod !== 'cash_on_delivery') {
                $walletService->addEarnings(
                    $order->seller,
                    (float) ($order->subtotal + $order->shipping_cost),
                    $order,
                    "Earning from order {$order->order_code} after appeal accepted."
                );
            }
        }

        return redirect()->back()->with('success', 'Banding diterima, dana diteruskan ke penjual.');
    }

    public function rejectAppeal(\Illuminate\Http\Request $request, Complaint $complaint)
    {
        $this->authorize('process', $complaint);
        
        $complaint->update([
            'status' => Complaint::STATUS_RESOLVED, // Status stays resolved (buyer wins)
            'resolution_note' => $complaint->resolution_note . "\n\n[BANDING DITOLAK] " . $request->input('appeal_resolution_note'),
        ]);

        return redirect()->back()->with('success', 'Banding ditolak, dana tetap dikembalikan ke pembeli.');
    }
}
