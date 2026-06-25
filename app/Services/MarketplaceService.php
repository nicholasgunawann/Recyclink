<?php

namespace App\Services;

use App\Models\WasteListing;
use Illuminate\Pagination\LengthAwarePaginator;

class MarketplaceService
{
    // ponytail: get approved, available listings with filters and pagination
    public function getApprovedAvailableListings(array $filters): LengthAwarePaginator
    {
        return $this->searchListings($filters);
    }

    // ponytail: retrieve single listing detail with views incremented
    public function getListingDetail(WasteListing $listing): WasteListing
    {
        $listing->incrementViewCount();
        return $listing->load(['seller.sellerProfile', 'category', 'images']);
    }

    // ponytail: search and filter listings
    public function searchListings(array $filters): LengthAwarePaginator
    {
        $page = request('page', 1);
        $filterHash = md5(serialize($filters));
        $cacheKey = "marketplace_listings_page_{$page}_{$filterHash}";

        return \Illuminate\Support\Facades\Cache::tags(['marketplace_listings'])->remember($cacheKey, 300, function () use ($filters) {
            return $this->performSearch($filters);
        });
    }

    protected function performSearch(array $filters): LengthAwarePaginator
    {
        $query = WasteListing::query()
            ->verified()
            ->available()
            ->with(['category', 'seller.sellerProfile', 'primaryImage']);

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $keyword = '%' . $filters['keyword'] . '%';
                $q->where('title', 'like', $keyword)
                  ->orWhere('description', 'like', $keyword);
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price_per_unit', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price_per_unit', '<=', $filters['price_max']);
        }

        if (!empty($filters['unit'])) {
            $query->where('unit', $filters['unit']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 12);
    }

    // ponytail: fetch related listings in same category
    public function getRelatedListings(WasteListing $listing, int $limit = 4)
    {
        return WasteListing::verified()
            ->available()
            ->where('category_id', $listing->category_id)
            ->where('id', '!=', $listing->id)
            ->with(['category', 'seller.sellerProfile', 'primaryImage'])
            ->latest()
            ->take($limit)
            ->get();
    }
}
