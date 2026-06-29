<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminReportController extends Controller implements HasMiddleware
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'role:admin',
        ];
    }

    // ponytail: view reports overview page
    public function index()
    {
        return view('admin.reports.index');
    }

    // ponytail: view transaction report
    public function transactions(Request $request)
    {
        $report = $this->reportService->getTransactionReport($request->all());
        return view('admin.reports.transactions', compact('report'));
    }

    // ponytail: view listing report
    public function listings(Request $request)
    {
        $report = $this->reportService->getListingReport($request->all());
        return view('admin.reports.listings', compact('report'));
    }

    // ponytail: view user status report
    public function users(Request $request)
    {
        $report = $this->reportService->getUserReport($request->all());
        return view('admin.reports.users', compact('report'));
    }
}
