<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WasteListing;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ponytail: get dashboard numbers with cache
    public function getAdminDashboardSummary(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('admin_dashboard_summary', 300, function () {
            return [
                'total_users' => User::count(),
                'total_listings' => WasteListing::count(),
                'total_transactions' => Order::count(),
                'total_revenue' => Order::where('order_status', Order::STATUS_COMPLETED)->sum('total_amount'),
            ];
        });
    }

    // ponytail: query transactions report
    public function getTransactionReport(array $filters): array
    {
        $query = Order::query()->with(['buyer', 'seller']);

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (!empty($filters['status'])) {
            $query->where('order_status', $filters['status']);
        }

        return [
            'data' => $query->latest()->get(),
            'total_amount' => $query->sum('total_amount'),
            'count' => $query->count(),
        ];
    }

    // ponytail: query listings report
    public function getListingReport(array $filters): array
    {
        $query = WasteListing::query()->with('seller');

        if (!empty($filters['status'])) {
            $query->where('availability_status', $filters['status']);
        }
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        return [
            'data' => $query->latest()->get(),
            'count' => $query->count(),
        ];
    }

    // ponytail: query users report
    public function getUserReport(array $filters): array
    {
        $query = User::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return [
            'data' => $query->latest()->get(),
            'count' => $query->count(),
        ];
    }

    // ponytail: calculate popular categories
    public function getPopularWasteCategories(array $filters): array
    {
        $categories = WasteCategory::select('waste_categories.*')
            ->selectRaw('count(waste_listings.id) as listings_count')
            ->join('waste_listings', 'waste_categories.id', '=', 'waste_listings.category_id')
            ->groupBy('waste_categories.id')
            ->orderByDesc('listings_count')
            ->take(5)
            ->get();

        return [
            'data' => $categories,
            'count' => $categories->count(),
        ];
    }
}
