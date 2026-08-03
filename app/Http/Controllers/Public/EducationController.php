<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\EducationContent;
use Illuminate\Support\Facades\Cache;

class EducationController extends Controller
{
    // ponytail: list all published articles, videos, and guides cached via Redis
    public function index()
    {
        $allContents = Cache::remember('public_education_contents', 1800, function () {
            return EducationContent::published()
                ->select(['id', 'admin_id', 'title', 'slug', 'thumbnail_url', 'content_type', 'status', 'published_at', 'excerpt', 'view_count'])
                ->with('admin:id,name')
                ->latest()
                ->get();
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
