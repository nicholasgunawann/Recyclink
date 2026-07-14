<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\EducationContent;

class EducationController extends Controller
{
    // ponytail: list all published articles, videos, and guides
    public function index()
    {
        $allContents = \Illuminate\Support\Facades\Cache::remember('education_published', 600, function () {
            return EducationContent::published()->latest()->get();
        });

        $articles = $allContents->where('content_type', 'article');
        $videos = $allContents->where('content_type', 'video');
        $guides = $allContents->where('content_type', 'guide');

        return view('pages.edukasi.index', compact('articles', 'videos', 'guides'));
    }

    // ponytail: view article detail page
    public function show(EducationContent $educationContent)
    {
        if ($educationContent->status !== 'published') {
            abort(404);
        }

        $educationContent->increment('view_count');

        return view('public.education.show', compact('educationContent'));
    }
}
