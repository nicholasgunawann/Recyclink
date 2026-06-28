<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\EducationContent;

class EducationController extends Controller
{
    // ponytail: list all published articles
    public function index()
    {
        $articles = EducationContent::published()->with(['admin', 'categories'])->latest()->paginate(9);
        return view('pages.edukasi.index', compact('articles'));
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
