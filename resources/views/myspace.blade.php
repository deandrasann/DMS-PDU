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
            <h5 class="mb-4">My Folders</h5>
            <div id="folderContainer" class="row g-3"></div>
        </div>

        {{-- SECTION: MY FILES --}}
        <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
            <h5 class="mb-4">My Files</h5>
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
                                    class="folder-item position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
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

    @section('modals')
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

    <!-- Upload Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true"
    data-bs-backdrop="false" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="uploadModalLabel">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body pt-0">
                <form id="uploadForm">
                    @csrf

                    <!-- Feedback Message -->
                    <div id="uploadMessage" class="alert d-none"></div>

                    <!-- Drop Zone -->
                    <div id="uploadArea" class="upload-box text-center mb-4 border border-2 border-dashed rounded-4 p-4"
                        style="border-color: #dee2e6; cursor: pointer; transition: all 0.2s ease;">
                        <input type="file" name="file" id="fileInput" class="d-none"
                            accept=".csv,.docx,.pdf,.pptx,.xlsx">

                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-light bg-opacity-50 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:70px; height:70px;">
                                <i class="ph ph-upload-simple text-orange fs-2" id="uploadIcon"></i>
                            </div>
                            <p class="mb-1 text-secondary fw-semibold">
                                Drag & drop files or <span class="text-orange text-decoration-underline">click
                                    here</span>
                            </p>
                            <small class="text-muted">Supported: CSV, DOCX, PDF, PPTX, XLSX</small>
                            <div id="fileName" class="mt-3 text-primary fw-semibold small"></div>
                        </div>
                    </div>

                    <!-- Title Input -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Save as</label>
                        <input type="text" name="title" id="title" class="form-control rounded-3"
                            placeholder="Title (Optional)">
                    </div>

                    <!-- Label Input -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Label</label>

                        <!-- Container untuk existing labels dan tombol/input -->
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <!-- Daftar label yang tersedia -->
                            <div id="existingLabels" class="d-flex flex-wrap gap-2">
                                <!-- Label akan diisi via JavaScript -->
                            </div>

                            <!-- Tombol Add Label (dipindahkan ke samping existing labels) -->
                            <div id="addLabelContainer">
                                <button type="button"
                                    class="btn btn-outline-primary rounded-3 d-flex align-items-center text-dark"
                                    id="addLabelBtn">
                                    <i class="ph ph-plus "></i>
                                </button>

                                <!-- Input Label Baru (hidden default) -->
                                <div id="newLabelContainer" class="d-none">
                                    <div class="input-group">
                                        <input type="text" id="newLabelInput" class="form-control rounded-3 mx-2"
                                            placeholder="Enter new label name" style="width: 150px;">
                                        <button type="button" class="btn btn-blue rounded-3 me-2 small"
                                            style="size: 12px" id="saveNewLabelBtn">
                                            Save
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary rounded-3 me-2 small"
                                            style="size: 12px" id="cancelNewLabelBtn">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Label yang dipilih (akan ditampilkan setelah dipilih) -->
                        <div id="selectedLabels" class="d-flex flex-wrap gap-2 mt-3">
                            <!-- Label yang dipilih akan muncul di sini -->
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-3  px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-blue rounded-3 px-4 fw-semibold">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Folder -->
<div class="modal fade" id="uploadFolderModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="uploadFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="uploadFolderModalLabel">
                    Create New Folder
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <img src="{{ asset('img/folder.svg') }}" alt="Folder Icon" style="width: 80px;">
                </div>

                <!-- Form untuk membuat folder -->
                <form id="createFolderForm">
                    @csrf
                    <div class="mb-3">
                        <label for="folderName" class="form-label fw-semibold">Folder Name</label>
                        <input type="text" class="form-control rounded-3" id="folderName" name="name"
                            placeholder="Enter folder name" required>
                    </div>

                    <!-- Feedback Message -->
                    <div id="folderMessage" class="alert d-none"></div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary border rounded-3 px-4"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-blue rounded-3 px-4 fw-semibold" id="createFolderBtn">Create
                    Folder</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true"
    data-bs-backdrop="false" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editFileModalLabel">Edit File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body pt-0">
                <form id="editFileForm">
                    @csrf
                    <input type="hidden" name="file_id" id="editFileId">

                    <!-- Feedback Message -->
                    <div id="editFileMessage" class="alert d-none"></div>

                    <!-- File Info Display -->
                    <div class="file-info-box border border-2 border-dashed rounded-4 p-4 mb-4"
                        style="border-color: #dee2e6;">
                        <div class="d-flex align-items-center">
                            <div class="bg-light bg-opacity-50 rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                style="width:60px; height:60px;">
                                <i class="ph ph-file text-orange fs-2" id="editFileIcon"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-semibold" id="editFileNameDisplay"></h6>
                                <small class="text-muted" id="editFileInfo"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Title Input -->
                    <div class="mb-3">
                        <label for="editTitle" class="form-label fw-semibold">File Name</label>
                        <input type="text" name="title" id="editTitle" class="form-control rounded-3"
                            placeholder="Enter file name" required>
                    </div>

                    <!-- Label Input -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Label</label>

                        <!-- Container untuk existing labels dan tombol/input -->
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <!-- Daftar label yang tersedia -->
                            <div id="editExistingLabels" class="d-flex flex-wrap gap-2">
                                <!-- Label akan diisi via JavaScript -->
                            </div>

                            <!-- Tombol Add Label -->
                            <div id="editAddLabelContainer">
                                <button type="button"
                                    class="btn btn-outline-primary rounded-3 d-flex align-items-center"
                                    id="editAddLabelBtn">
                                    <i class="ph ph-plus me-2"></i> Add Label
                                </button>

                                <!-- Input Label Baru (hidden default) -->
                                <div id="editNewLabelContainer" class="d-none">
                                    <div class="input-group">
                                        <input type="text" id="editNewLabelInput"
                                            class="form-control rounded-3 mx-2" placeholder="Enter new label name"
                                            style="width: 150px;">
                                        <button type="button" class="btn btn-blue rounded-3 me-2 small"
                                            style="size: 12px" id="editSaveNewLabelBtn">
                                            Save
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary rounded-3 me-2 small"
                                            style="size: 12px" id="editCancelNewLabelBtn">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Label yang dipilih -->
                        <div id="editSelectedLabels" class="d-flex flex-wrap gap-2 mt-3">
                            <!-- Label yang dipilih akan muncul di sini -->
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-blue rounded-3 px-4 fw-semibold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    @endsection
