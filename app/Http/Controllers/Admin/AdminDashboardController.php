<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminDashboardController extends Controller implements HasMiddleware
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

    // ponytail: load dashboard index view with basic stats
    public function index()
    {
        $stats = $this->reportService->getAdminDashboardSummary();
        return view('admin.dashboard', compact('stats'));
    }
}
