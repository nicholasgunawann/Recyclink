<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WasteListing;
use App\Models\EducationContent;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    // ponytail: homepage view cached via Redis
    public function index()
    {
        return view('public.home', [
            'recentListings' => WasteListing::verified()
                ->available()
                ->with(['category', 'seller.sellerProfile', 'primaryImage', 'images'])
                ->latest()
                ->take(4)
                ->get(),
            'featuredArticles' => EducationContent::published()->with('admin')->latest()->take(3)->get(),
        ]);
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
            $targetEmail = env('CONTACT_RECIPIENT_EMAIL', 'therecyclink@gmail.com');
            \Illuminate\Support\Facades\Mail::to($targetEmail)
                ->send(new \App\Mail\ContactUsEmail($validated));
            
            \Illuminate\Support\Facades\Log::info('Contact form submitted and mail sent to ' . $targetEmail, $validated);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda!');
    }
}

