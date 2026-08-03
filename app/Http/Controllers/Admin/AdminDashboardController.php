<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Cache;

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

    // ponytail: load dashboard index view with summary stats cached via Redis
    public function index()
    {
        $dashboardData = Cache::remember('admin_dashboard_summary', 300, function () {
            return [
                'stats' => $this->reportService->getAdminDashboardSummary(),
                'recentOrders' => \App\Models\Order::with(['buyer', 'seller', 'items'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'pendingVerificationsCount' => \App\Models\WasteListing::where('verification_status', 'pending')->count(),
                'pendingComplaintsCount' => \App\Models\Complaint::where('status', 'pending')->count(),
            ];
        });

        return view('admin.dashboard', $dashboardData);
    }
}
