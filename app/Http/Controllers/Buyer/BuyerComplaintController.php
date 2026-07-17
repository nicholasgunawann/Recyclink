<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Complaint;
use Illuminate\Support\Str;

class BuyerComplaintController extends Controller
{
    public function create(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }
        if (!in_array($order->order_status, [Order::STATUS_PAID, Order::STATUS_PROCESSING])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak dapat diajukan komplain.');
        }

        return view('buyer.complaints.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($order->order_status, [Order::STATUS_PAID, Order::STATUS_PROCESSING])) {
            return redirect()->back()->with('error', 'Pesanan ini tidak dapat diajukan komplain.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'complaint_type' => 'required|string',
            'description' => 'required|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480', // 20MB max
        ]);

        $evidenceUrl = null;
        if ($request->hasFile('evidence')) {
            $evidenceUrl = $request->file('evidence')->store('complaints', 'public');
        }

        $complaint = Complaint::create([
            'complaint_number' => 'CMP-' . strtoupper(Str::random(8)),
            'order_id' => $order->id,
            'complainant_id' => auth()->id(),
            'respondent_id' => $order->seller_id,
            'subject' => $request->input('subject'),
            'complaint_type' => $request->input('complaint_type'),
            'description' => $request->input('description'),
            'evidence_url' => $evidenceUrl,
            'status' => Complaint::STATUS_OPEN,
        ]);

        // Change order status to disputed
        $order->update(['order_status' => Order::STATUS_DISPUTED]);

        return redirect()->route('buyer.complaints.show', $complaint)->with('success', 'Komplain berhasil diajukan. Silakan berdiskusi dengan penjual di Pusat Resolusi.');
    }

    public function index()
    {
        $complaints = Complaint::where('complainant_id', auth()->id())->with('order.seller')->latest()->paginate(10);
        return view('buyer.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->complainant_id !== auth()->id()) {
            abort(403);
        }
        
        $complaint->load(['order', 'respondent', 'messages.user']);
        return view('buyer.complaints.show', compact('complaint'));
    }

    public function storeMessage(Request $request, Complaint $complaint)
    {
        if ($complaint->complainant_id !== auth()->id()) {
            abort(403);
        }

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

        return redirect()->back();
    }
}
