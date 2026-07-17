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
}
