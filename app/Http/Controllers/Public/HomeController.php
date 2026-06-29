<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Models\EducationContent;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    // ponytail: homepage view — cached in Redis to avoid slow remote DB on every visit
    public function index()
    {
        $recentListings = Cache::remember('home_recent_listings', 600, function () {
            return WasteListing::verified()
                ->available()
                ->with(['category', 'seller.sellerProfile', 'primaryImage'])
                ->latest()
                ->take(4)
                ->get();
        });

        $featuredArticles = Cache::remember('home_featured_articles', 600, function () {
            return EducationContent::published()->latest()->take(3)->get();
        });

        return view('public.home', compact('recentListings', 'featuredArticles'));
    }

    public function tentang()
    {
        return view('pages.tentang.index');
    }
}

