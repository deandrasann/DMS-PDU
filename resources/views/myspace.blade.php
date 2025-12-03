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
    <!-- Advanced Share Modal – Folder & File Preview Berbeda -->
    <div class="modal fade" id="advancedShareModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
            <div class="modal-content border-0 rounded-4 shadow-lg" style="background: #FFFFFF; overflow: hidden;">

                <!-- Header -->
                <div class="modal-header border-0 pb-0 px-4 pt-4 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body px-4 pt-3 pb-5" style="margin-left: 30px">
                    <h5 class="fw-bold text-dark mb-5">Advanced Share</h5>

                    <!-- PREVIEW DINAMIS -->
                    <div class="d-flex justify-content-center mb-5">
                        <div id="preview-container" style="width: 230px;">

                            <!-- PREVIEW FOLDER (Style Asli Kamu) -->
                            <div id="folder-preview" class="folder-card position-relative" style="cursor: default;">
                                <img src="{{ asset('img/folder.svg') }}" alt="Folder"
                                    class="img-fluid w-100 h-100 object-fit-contain"
                                    style="min-height: 150px; min-width: 170px;">

                                <div
                                    class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <p class="fw-normal mt-2 mb-0 text-truncate small lh-sm text-dark"
                                            id="preview-title"></p>
                                        <small class="fw-light text-muted d-block" id="preview-subtitle"></small>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <i class="ph ph-dots-three-vertical fs-5 text-muted opacity-70"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- PREVIEW FILE (Identik Card File di MySpace) -->
                            <div id="file-preview"
                                class="card rounded-4 border-dark-subtle border-1 shadow-sm overflow-hidden"
                                style="width: 180px; height: 220px; background-color: #F2F2F0; display: none;">
                                <div class="mt-3 mx-2 preview-container" style="height: 120px; overflow: hidden;">
                                    <div id="file-thumbnail-wrapper"
                                        class="d-flex justify-content-center align-items-center h-100 w-100">
                                        <!-- Thumbnail akan diisi otomatis oleh JS: img, canvas (PDF), atau video -->
                                    </div>
                                    <!-- Loading spinner -->
                                    <div id="file-thumbnail-loading"
                                        class="position-absolute top-50 start-50 translate-middle d-none">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <i id="file-small-icon" class="me-2 text-dark"></i>
                                        <span class="fw-semibold text-truncate small" id="file-name-display"></span>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span id="file-badge"
                                            class="badge bg-secondary rounded-2 px-2"><small></small></span>
                                        <span class="text-muted small" id="file-size-display"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Who can access -->
                    <!-- Ganti seluruh bagian "Who can access" dengan ini -->
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <label class="fw-semibold text-dark mb-0"
                            style="width: 140px; flex-shrink: 0; font-size: 0.95rem; padding-top: 12px;">
                            Who can access
                        </label>
                        <div class="flex-fill position-relative">
                            <!-- INI WRAPPER UTAMA (bukan input biasa) -->
                            <div id="email-input-wrapper"
                                class="form-control rounded-4 border-0 shadow-sm d-flex flex-wrap align-items-center gap-2 pe-5"
                                style="min-height: 46px; background: #F8F9FA; padding: 8px 12px; cursor: text;">

                                <!-- Pill akan muncul di sini oleh JS -->
                                <div id="selected-emails-container" class="d-flex flex-wrap gap-2"></div>

                                <!-- Input asli (hanya untuk ketik, tidak terlihat border) -->
                                <input type="text" id="add-email-input"
                                    class="border-0 outline-0 flex-grow-1 bg-transparent"
                                    style="min-width: 120px; height: 30px; font-weight: 500; font-size: 0.95rem;"
                                    placeholder="Add people by email" autocomplete="off">
                            </div>

                            <!-- Icon panah bawah -->
                            <i class="ph ph-caret-down position-absolute top-50 end-0 translate-middle-y me-4 text-muted z-3"
                                style="font-size: 1.4rem; pointer-events: none;"></i>

                            <!-- Dropdown hasil pencarian -->
                            <div id="email-suggestions"
                                class="position-absolute start-0 end-0 bg-white shadow-lg rounded-4 mt-1 border"
                                style="top: 100%; z-index: 1050; max-height: 280px; overflow-y: auto; display: none;">
                                <div class="p-2" id="suggestions-list"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <button type="button"
                            class="btn btn-outline-primary rounded-4 px-4 py-2 d-flex align-items-center gap-2 border-2 fw-medium">
                            <i class="ph ph-link"></i> Copy Link
                        </button>
                        <button type="button" id="share-done-btn" class="btn btn-primary rounded-4 px-5 py-2 fw-medium shadow-sm">
                            Done
                        </button>
                    </div>
                </div>
            </div>
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
    <div class="modal fade" id="renameFolderModal" tabindex="-1" aria-labelledby="renameFolderModalLabel"
        aria-hidden="true" data-bs-backdrop="false">
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
                            <input type="text" class="form-control rounded-3 border-dark-subtle" id="newFolderName"
                                name="new_name" required placeholder="Enter new folder name">
                            <div class="form-text text-muted">
                                Folder name cannot contain special characters.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal">Cancel</button>
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
