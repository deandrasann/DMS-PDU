@extends('layouts.app')

@section('title', 'Recommended Files')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Home</h4>
        </div>
    </div>

    @if(isset($error) && $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    {{-- SECTION: RECOMMENDED FILES --}}
    <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
        <h5 class=" mb-4">
            Recommended Files
        </h5>
        <div id="fileContainer" class="row g-3 ms-1 me-2">
            {{-- Loading state --}}
            <div class="col-12 text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Loading recommended files...</p>
            </div>
        </div>
    </div>
</div>

{{-- TEMPLATE UNTUK KOSONG --}}
<template id="empty-template">
    <div class="d-flex flex-column grow justify-content-center align-items-center text-center p-5 text-muted">
        <i class="ph ph-star" style="font-size: 80px; color: #9E9E9C;"></i>
        <p class="mt-3">No recommended files available.</p>
    </div>
</template>

{{-- Load PDF.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

{{-- Load JavaScript file universal --}}
<script src="{{ asset('js/myspace.js') }}"></script>

<script>
    // Set PDF.js worker path
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    // Pass PHP variables to JavaScript
    window.token = "{{ $token ?? '' }}";
    window.currentPath = "";
    window.isRecommendedPage = true;
</script>
@endsection
