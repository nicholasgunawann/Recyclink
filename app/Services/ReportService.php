<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WasteListing;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ponytail: get dashboard numbers — one query instead of four to minimize remote DB round trips
    public function getAdminDashboardSummary(): array
    {
        $row = DB::selectOne('
            SELECT
                (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL) as total_users,
                (SELECT COUNT(*) FROM waste_listings WHERE deleted_at IS NULL) as total_listings,
                (SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL) as total_transactions,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status IN (?, ?, ?) AND deleted_at IS NULL) as total_revenue
        ', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED]);

        return [
            'total_users'        => (int) $row->total_users,
            'total_listings'     => (int) $row->total_listings,
            'total_transactions' => (int) $row->total_transactions,
            'total_revenue'      => (float) $row->total_revenue,
        ];
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

    public function getAnalyticsReport(array $filters): array
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        // Base queries
        $userQuery = User::query();
        $listingQuery = WasteListing::query()->where('availability_status', WasteListing::AVAILABILITY_AVAILABLE);
        $orderQuery = Order::query()->whereIn('order_status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED]);
        
        if ($startDate) {
            $userQuery->whereDate('created_at', '>=', $startDate);
            $listingQuery->whereDate('created_at', '>=', $startDate);
            $orderQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $userQuery->whereDate('created_at', '<=', $endDate);
            $listingQuery->whereDate('created_at', '<=', $endDate);
            $orderQuery->whereDate('created_at', '<=', $endDate);
        }

        // 1. Total Pengguna (Penjual & Pembeli)
        $totalUsers = $userQuery->count();
        $totalSellers = (clone $userQuery)->role('seller')->count();
        $totalBuyers = (clone $userQuery)->role('buyer')->count();

        // 2. Total Listing (Limbah Aktif)
        $totalListings = $listingQuery->count();

        // 3. Total Transaksi (Transaksi Berhasil)
        $totalTransactions = $orderQuery->count();

        // 4. Pendapatan Platform (Total platform_fee)
        $platformRevenue = $orderQuery->sum('platform_fee');

        // 5. Jenis Limbah Populer (dari transaksi atau listing)
        // Kita hitung dari jumlah transaksi per kategori limbah
        $popularCategories = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('waste_listings', 'order_items.listing_id', '=', 'waste_listings.id')
            ->join('waste_categories', 'waste_listings.category_id', '=', 'waste_categories.id')
            ->whereIn('orders.order_status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->when($startDate, fn($q) => $q->whereDate('orders.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('orders.created_at', '<=', $endDate))
            ->whereNull('orders.deleted_at')
            ->select('waste_categories.category_name', DB::raw('COUNT(orders.id) as total_sales'))
            ->groupBy('waste_categories.id', 'waste_categories.category_name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        // 6. Wilayah Aktif (Kota dengan transaksi terbanyak)
        $activeRegions = DB::table('orders')
            ->join('waste_listings', 'orders.seller_id', '=', 'waste_listings.seller_id')
            ->whereIn('orders.order_status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->when($startDate, fn($q) => $q->whereDate('orders.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('orders.created_at', '<=', $endDate))
            ->whereNull('orders.deleted_at')
            ->select('waste_listings.city', DB::raw('COUNT(DISTINCT orders.id) as total_transactions'))
            ->groupBy('waste_listings.city')
            ->orderByDesc('total_transactions')
            ->take(5)
            ->get();

        // Chart Data (Transactions over time)
        $chartData = $this->getChartData($startDate, $endDate);

        return [
            'total_users' => $totalUsers,
            'total_sellers' => $totalSellers,
            'total_buyers' => $totalBuyers,
            'total_listings' => $totalListings,
            'total_transactions' => $totalTransactions,
            'platform_revenue' => $platformRevenue,
            'popular_categories' => $popularCategories,
            'active_regions' => $activeRegions,
            'chart_data' => $chartData,
            'filters' => $filters,
        ];
    }

    private function getChartData($startDate, $endDate)
    {
        // Simple grouping by date for completed orders
        $query = Order::query()->whereIn('order_status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED]);
        
        if ($startDate) $query->whereDate('created_at', '>=', $startDate);
        if ($endDate) $query->whereDate('created_at', '<=', $endDate);
        
        $data = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        return [
            'labels' => $data->pluck('date')->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }
}
