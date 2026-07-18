<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Models\EducationContent;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    // ponytail: homepage view without redis cache (railway redis timeout issue)
    public function index()
    {
        $recentListings = WasteListing::verified()
            ->available()
            ->with(['category', 'seller.sellerProfile', 'primaryImage'])
            ->latest()
            ->take(4)
            ->get();

        $featuredArticles = EducationContent::published()->latest()->take(3)->get();

        return view('public.home', compact('recentListings', 'featuredArticles'));
    }

    public function tentang()
    {
        return view('pages.tentang.index');
    }

    public function submitContact(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Saat menggunakan Resend Sandbox (belum ada domain verify),
        // email HANYA BOLEH dikirim ke email terdaftar (therecyclink@gmail.com).
        // Oleh karena itu, kita tidak bisa mengirim email otomatis ke $validated['email'] pengguna.
        
        try {
            // Disabled to prevent slow loading and 500 errors in production/sandbox
            // \Illuminate\Support\Facades\Mail::to('therecyclink@gmail.com')
            //     ->send(new \App\Mail\ContactUsEmail($validated));
            
            \Illuminate\Support\Facades\Log::info('Contact form submitted:', $validated);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda!');
    }
}

