<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Models\EducationContent;

class HomeController extends Controller
{
    // ponytail: homepage view with latest verified listings and featured content
    public function index()
    {
        $recentListings = WasteListing::verified()->available()->with(['category', 'seller.sellerProfile', 'primaryImage'])->latest()->take(6)->get();
        $featuredArticles = EducationContent::published()->latest()->take(3)->get();

        return view('public.home', compact('recentListings', 'featuredArticles'));
    }
}
