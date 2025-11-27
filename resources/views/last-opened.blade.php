@extends('layouts.app')

@section('title', 'Last Opened')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Last Opened</h2>
        </div>
    </div>

    @if(isset($error) && $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    {{-- SECTION: RECENT FOLDERS --}}
    <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
        <h4 class="fw-semibold mb-4">
            <i class="ph ph-folder-open me-2"></i>Last Opened Folders
        </h4>
        <div id="folderContainer" class="row g-3">
            {{-- Loading state --}}
            <div class="col-12 text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Loading recent folders...</p>
            </div>
        </div>
    </div>

    {{-- SECTION: RECENT FILES --}}
    <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
        <h4 class="fw-semibold mb-4">
            <i class="ph ph-file me-2"></i>Last Opened Files
        </h4>
        <div id="fileContainer" class="row g-3 ms-1 me-2">
            {{-- Loading state --}}
            <div class="col-12 text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Loading recent files...</p>
            </div>
        </div>
    </div>
</div>

{{-- TEMPLATE UNTUK KOSONG --}}
<template id="empty-template">
    <div class="d-flex flex-column grow justify-content-center align-items-center text-center p-5 text-muted">
        <i class="ph ph-clock" style="font-size: 80px; color: #9E9E9C;"></i>
        <p class="mt-3">No recently opened items.</p>
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
    window.isLastOpenedPage = true;

    // Debug info
    console.log('Last Opened Page Initialized:', {
        token: window.token ? 'exists' : 'missing',
        isLastOpenedPage: window.isLastOpenedPage
    });
</script>
@endsection
