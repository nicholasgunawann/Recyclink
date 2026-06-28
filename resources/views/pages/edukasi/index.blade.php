@extends('layouts.master')
@section('title', 'Pusat Edukasi – Recyclink')
@section('content')
    @include('pages.edukasi.hero')
    @include('pages.edukasi.tabs')
    
    <div id="tab-content-artikel" class="tab-content hidden">
        @include('pages.edukasi.artikel')
    </div>

    <div id="tab-content-video" class="tab-content hidden">
        @include('pages.edukasi.video')
    </div>
    
    <div id="tab-content-panduan" class="tab-content block">
        @include('pages.edukasi.panduan')
    </div>

    <div id="tab-content-faq" class="tab-content hidden">
        @include('pages.edukasi.faq')
    </div>

    @include('pages.edukasi.newsletter')
@endsection
