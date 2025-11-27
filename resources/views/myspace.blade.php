@extends('layouts.app')

@section('title', 'My Space')

@section('content')
@php
    $segments = array_filter(explode('/', $currentPath));
    $accum = '';
@endphp

@if (!empty($breadcrumb) && count($breadcrumb) > 1)
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            @foreach ($breadcrumb as $crumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active text-dark text-decoration-none" aria-current="page">
                        {{ $crumb['name'] }}
                    </li>
                @else
                    <li class="breadcrumb-item text-dark">
                        <a href="{{ $crumb['url'] }}" class="text-decoration-none text-dark">
                            {{ $crumb['name'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif

<div class="container py-4">
    {{-- SECTION: MY FOLDERS --}}
    <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
        <h4 class="fw-semibold mb-4">My Folders</h4>
        <div id="folderContainer" class="row g-3"></div>
    </div>

    {{-- SECTION: MY FILES --}}
    <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
        <h4 class="fw-semibold mb-4">My Files</h4>
        <div id="fileContainer" class="row g-3 ms-1 me-2"></div>
    </div>
</div>

{{-- TEMPLATE UNTUK KOSONG --}}
<template id="empty-template">
    <div class="d-flex flex-column grow justify-content-center align-items-center text-center p-5 text-muted">
        <i class="ph ph-folder-open" style="font-size: 80px; color: #9E9E9C;"></i>
        <p class="mt-3">Tidak ada item di sini.</p>
    </div>
</template>



{{-- Load PDF.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

{{-- Load JavaScript file terpisah --}}
<script src="{{ asset('js/myspace.js') }}"></script>

<script>
    // Set PDF.js worker path
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    // Pass PHP variables to JavaScript
    window.token = "{{ $token }}";
    window.currentPath = "{{ $currentPath }}";
</script>
@endsection
