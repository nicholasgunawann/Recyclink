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

    // ponytail: view complaint details
    public function show(Complaint $complaint)
    {
        $complaint->load(['complainant', 'respondent', 'order']);
        return view('admin.complaints.show', compact('complaint'));
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
}
