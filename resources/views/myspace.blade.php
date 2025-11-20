@extends('layouts.app')

@section('title', 'My Space')

@section('content')
@php
    $segments = array_filter(explode('/', $currentPath));
    $accum = '';
@endphp

@if (!empty($breadcrumb) && $currentPath !== '')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            @foreach ($breadcrumb as $crumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active">{{ $crumb['name'] }}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $crumb['path'] ? route('myspace.subfolder', ['path' => $crumb['path']]) : route('myspace') }}">
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

{{-- MODAL RENAME FOLDER --}}
<div class="modal fade" id="renameFolderModal" tabindex="-1" aria-labelledby="renameFolderModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="renameFolderModalLabel">Rename Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form id="renameFolderForm">
                    <input type="hidden" id="renameFolderId" name="folder_id">
                    <div class="mb-3">
                        <label for="newFolderName" class="form-label fw-medium">New Folder Name</label>
                        <input type="text" class="form-control rounded-3 border-dark-subtle"
                               id="newFolderName" name="new_name" required
                               placeholder="Enter new folder name">
                        <div class="form-text text-muted">
                            Folder name cannot contain special characters.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-blue rounded-3 px-4" id="confirmRenameFolder">Rename</button>
            </div>
        </div>
    </div>
</div>

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
