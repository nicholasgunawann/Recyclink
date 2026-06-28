@extends('layouts.master')

@section('title', 'Recyclink — Cuan Bertambah, Lingkungan Terjaga')

@section('content')

    {{-- 1. Hero & Search --}}
    @include('pages.beranda.hero')

    {{-- 2. Kategori Limbah --}}
    @include('pages.beranda.kategori')

    {{-- 3. Limbah Terbaru --}}
    @include('pages.beranda.limbah-terbaru')

    {{-- 4. Keunggulan Recyclink --}}
    @include('pages.beranda.keunggulan')

    {{-- 5. Dampak & Tujuan --}}
    @include('pages.beranda.cara-kerja')

    {{-- 6. Testimoni & Mitra --}}
    @include('pages.beranda.testimoni')

@endsection
