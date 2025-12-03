@extends('layouts.app')

@section('title', 'Last Opened')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class=" mb-1">Last Opened</h4>
        </div>
    </div>

    @if(isset($error) && $error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    {{-- SECTION: RECENT FOLDERS --}}
    <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
        <h5 class="mb-4">
            Last Opened Folders
        </h5>
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
        <h5 class="mb-4">
            Last Opened Files
        </h5>
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
