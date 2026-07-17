<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Routing\Controllers\HasMiddleware;

class SellerComplaintController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:seller',
        ];
    }

    public function index()
    {
        $complaints = Complaint::where('respondent_id', auth()->id())->with('order.buyer')->latest()->paginate(10);
        return view('seller.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->respondent_id !== auth()->id()) {
            abort(403);
        }
        
        $complaint->load(['order', 'complainant', 'messages.user']);
        return view('seller.complaints.show', compact('complaint'));
    }

    public function storeMessage(Request $request, Complaint $complaint)
    {
        if ($complaint->respondent_id !== auth()->id()) {
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

    public function storeAppeal(Request $request, Complaint $complaint)
    {
        if ($complaint->respondent_id !== auth()->id()) {
            abort(403);
        }

        // Only allow appeal if complaint is resolved (buyer won) and within 7 days
        if ($complaint->status !== Complaint::STATUS_RESOLVED || !$complaint->resolved_at) {
            return redirect()->back()->with('error', 'Status resolusi tidak valid untuk diajukan banding.');
        }

        if ($complaint->resolved_at->diffInDays(now()) > 7) {
            return redirect()->back()->with('error', 'Masa pengajuan banding telah melewati batas 7 hari.');
        }

        $request->validate([
            'appeal_reason' => 'required|string',
            'appeal_evidence' => 'required|file|mimes:mp4,jpg,jpeg,png|max:20480',
        ]);

        $evidenceUrl = null;
        if ($request->hasFile('appeal_evidence')) {
            $evidenceUrl = $request->file('appeal_evidence')->store('appeals', 'public');
        }

        $complaint->update([
            'status' => Complaint::STATUS_APPEALED,
            'appeal_reason' => $request->input('appeal_reason'),
            'appeal_evidence_url' => $evidenceUrl,
            'appealed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Banding berhasil diajukan. Admin akan meninjau bukti Anda.');
    }
}
