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
                <li class="breadcrumb-item">
                    <a href="{{ route('myspace') }}" class="text-dark text-decoration-none">
                        MySpace
                    </a>
                </li>
                @foreach ($breadcrumb as $crumb)
                    @if ($loop->last)
                        <li class="breadcrumb-item active text-dark">
                            {{ $crumb['name'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            @if (!empty($crumb['path']))
                                <a href="{{ route('myspace.subfolder', ['path' => $crumb['path']]) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $crumb['name'] }}
                                </a>
                            @else
                                <a href="{{ route('myspace') }}" class="text-dark text-decoration-none">
                                    {{ $crumb['name'] }}
                                </a>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="container py-4">
        {{-- ========== SECTION: MY FOLDERS ========== --}}
        <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
            <h4 class="fw-semibold mb-4">My Folders</h4>
            <div id="folderContainer" class="row g-3"></div>
        </div>

        {{-- ========== SECTION: MY FILES ========== --}}
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
                            <div id="file-preview" class="card rounded-4 border-dark-subtle border-1"
                                style="width: 180px; height: 220px; background-color: #F2F2F0; display: none;">
                                <div class="mt-3 mx-2 preview-container" style="height: 120px;">
                                    <div id="file-thumbnail-wrapper" class="d-flex justify-content-center align-items-center h-100 w-100">
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
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <label class="fw-semibold text-dark mb-0" style="width: 140px; flex-shrink: 0; font-size: 0.95rem;">
                            Who can access
                        </label>
                        <div class="flex-fill position-relative">
                            <input type="text" id="add-email-input"
                                class="form-control rounded-4 border-0 shadow-sm text-dark w-100"
                                style="height: 46px; padding: 0 48px 0 20px; background: #F8F9FA; font-weight: 500; font-size: 0.95rem;"
                                placeholder="Add Email" autocomplete="off">
                            <i class="ph ph-caret-down position-absolute top-50 end-0 translate-middle-y me-4 text-muted"
                                style="font-size: 1.4rem; pointer-events: none;"></i>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <button type="button"
                            class="btn btn-outline-primary rounded-4 px-4 py-2 d-flex align-items-center gap-2 border-2 fw-medium">
                            <i class="ph ph-link"></i> Copy Link
                        </button>
                        <button type="button" class="btn btn-primary rounded-4 px-5 py-2 fw-medium shadow-sm">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TEMPLATE UNTUK KOSONG ========== --}}
    <template id="empty-template">
        <div class="d-flex flex-column grow justify-content-center align-items-center text-center p-5 text-muted">
            <i class="ph ph-folder-open" style="font-size: 80px; color: #9E9E9C;"></i>
            <p class="mt-3">Tidak ada item di sini.</p>
        </div>
    </template>



    {{-- Load PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    <script>
        // Set PDF.js worker path
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

        document.addEventListener("DOMContentLoaded", async () => {
            const folderContainer = document.getElementById("folderContainer");
            const fileContainer = document.getElementById("fileContainer");
            const token = "{{ $token }}";
            const currentPath = "{{ $currentPath }}";
            const emptyTemplate = document.getElementById("empty-template").content.cloneNode(true);

            console.log('Current Path:', currentPath);
            console.log('Token available:', !!token);

            if (!token) {
                console.error('No token available');
                folderContainer.innerHTML =
                    `<p class="text-danger">Token tidak tersedia. Silakan login ulang.</p>`;
                fileContainer.innerHTML =
                    `<p class="text-danger">Token tidak tersedia. Silakan login ulang.</p>`;
                return;
            }

            try {
                const baseUrl = "https://pdu-dms.my.id/api/my-files";
                const url = currentPath ? `${baseUrl}/${currentPath}` : baseUrl;

                console.log('Fetching from:', url);

                const response = await fetch(url, {
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        alert('Session expired. Please login again.');
                        window.location.href = "{{ route('signin') }}";
                        return;
                    }
                    throw new Error(`Gagal memuat data: ${response.status} ${response.statusText}`);
                }

                const data = await response.json();
                console.log('API Response:', data);

                const folders = data.files?.filter(f => f.is_folder) || [];
                const files = data.files?.filter(f => !f.is_folder) || [];

                console.log('Folders:', folders);
                console.log('Files:', files);

                // ===== Render Folders =====
                folderContainer.innerHTML = '';
                if (folders.length === 0) {
                    const empty = emptyTemplate.cloneNode(true);
                    empty.querySelector("i").className = "ph ph-folder-open";
                    empty.querySelector("p").textContent = "Create a folder to get organized";
                    folderContainer.appendChild(empty);
                } else {
                    folders.forEach(folder => {
                        const col = document.createElement("div");
                        col.className = "col-6 col-sm-4 col-md-3 col-lg-2 folder-item";

                        const folderPath = folder.id

                        col.innerHTML = `
                        <div class="position-relative">
                            <div class="folder-card" style="cursor: pointer;">
                                <img src="{{ asset('img/folder.svg') }}" alt="Folder" class="img-fluid w-100 h-100 object-fit-contain" style="min-height: 100px; min-width:120px">
                                <div class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <p class="fw-normal mt-2 mb-0 text-truncate" title="${folder.name}">${folder.name}</p>
                                        <small class="fw-light">${folder.size}</small>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="dropdown">
                                            <button class="btn btn-link ms-auto text-dark p-0"
                                                    data-bs-toggle="dropdown"
                                                    data-bs-display="static">
                                                <i class="ph ph-dots-three-vertical fs-5 text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 folder-open-btn"
                                                       href="/myspace/${folderPath}">
                                                        <i class="ph ph-arrow-up-right fs-5"></i> Open
                                                    </a>
                                                </li>
                                                <li class="dropdown-submenu position-relative">
                                                    <a class="dropdown-item d-flex align-items-center gap-2 folder-info-btn"
                                                       href="#"
                                                       data-folder-id="${folder.id}"
                                                       data-folder-name="${folder.name}">
                                                        <i class="ph ph-info fs-5"></i>Get Info
                                                    </a>
                                                    <div class="folder-info-panel" style="display: none; position: absolute; left: 100%; top: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 10000; border: 1px solid #e9ecef;">
                                                        <div class="p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h6 class="fw-semibold mb-0">Folder Information</h6>
                                                                <button type="button" class="btn-close folder-close-info-panel" style="font-size: 0.7rem;"></button>
                                                            </div>

                                                            <div class="text-center mb-3">
                                                                <div class="folder-preview-icon mx-auto mb-2">
                                                                    <i class="ph ph-folder fs-1 text-warning"></i>
                                                                </div>
                                                                <h6 class="fw-semibold mb-1 text-truncate">${folder.name}</h6>
                                                                <small class="text-muted">Folder • ${folder.size}</small>
                                                            </div>

                                                            <div class="folder-details">
                                                                <div class="detail-item mb-2">
                                                                    <label class="text-muted small fw-semibold mb-1">Folder Name</label>
                                                                    <p class="mb-0 fw-medium text-truncate">${folder.name}</p>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="detail-item mb-2">
                                                                            <label class="text-muted small fw-semibold mb-1">Type</label>
                                                                            <p class="mb-0 fw-medium">Folder</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="detail-item mb-2">
                                                                            <label class="text-muted small fw-semibold mb-1">Size</label>
                                                                            <p class="mb-0 fw-medium">${folder.size}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="detail-item mb-2">
                                                                    <label class="text-muted small fw-semibold mb-1">Location</label>
                                                                    <p class="mb-0 fw-medium" id="folder-location-${folder.id}">Loading...</p>
                                                                </div>

                                                                <div class="detail-item mb-2">
                                                                    <label class="text-muted small fw-semibold mb-1">Created Date</label>
                                                                    <p class="mb-0 fw-medium">${folder.created_at || 'Unknown'}</p>
                                                                </div>

                                                                <div class="detail-item mb-2">
                                                                    <label class="text-muted small fw-semibold mb-1">Last Modified</label>
                                                                    <p class="mb-0 fw-medium">${folder.updated_at || 'Unknown'}</p>
                                                                </div>

                                                                <div class="detail-item mb-2">
                                                                    <label class="text-muted small fw-semibold mb-1">Owner</label>
                                                                    <p class="mb-0 fw-medium">${folder.owner === 'me' ? 'You' : 'Shared'}</p>
                                                                </div>

                                                                <div class="detail-item">
                                                                    <label class="text-muted small fw-semibold mb-1">Who has access</label>
                                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                                        <div class="access-indicator bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                                                            <i class="ph ph-lock text-white" style="font-size: 10px;"></i>
                                                                        </div>
                                                                        <span class="fw-medium small">Private</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 advanced-share-btn"
                                                    href="#"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#advancedShareModal"
                                                    data-folder-id="${folder.id}"
                                                    data-folder-name="${folder.name}"
                                                    data-folder-items="${folder.size}">
                                                        <i class="ph ph-share-network fs-5"></i> Share
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 folder-download-btn"
                                                       href="#"
                                                       data-id="${folder.id}"
                                                       data-name="${folder.name}">
                                                        <i class="ph ph-download fs-5"></i> Download
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger d-flex align-items-center gap-2 folder-delete-btn"
                                                       href="#"
                                                       data-id="${folder.id}"
                                                       data-name="${folder.name}">
                                                        <i class="ph ph-trash fs-5"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                        folderContainer.appendChild(col);
                    });
                }

                // ===== Render Files =====
                fileContainer.innerHTML = '';
                if (files.length === 0) {
                    const empty = emptyTemplate.cloneNode(true);
                    empty.querySelector("i").className = "ph ph-file";
                    empty.querySelector("p").textContent = "Upload your first file to begin";
                    fileContainer.appendChild(empty);
                } else {
                    files.forEach(file => {
                        const card = document.createElement("div");
                        card.className = "card rounded-4 border-dark-subtle border-1 me-3 file-card";
                        card.style.width = "180px";
                        card.style.height = "220px";
                        card.style.backgroundColor = "#F2F2F0";
                        card.style.cursor = "pointer";

                        let fileIcon = "ph-file";
                        let fileType = "File";

                        if (file.mime) {
                            if (file.mime.includes("pdf")) {
                                fileIcon = "ph-file-pdf";
                                fileType = "PDF";
                            } else if (file.mime.includes("docs") || file.mime.includes("document")) {
                                fileIcon = "ph-file-doc";
                                fileType = "DOC";
                            } else if (file.mime.includes("xlsx") || file.mime.includes(
                                    "spreadsheet")) {
                                fileIcon = "ph-file-xls";
                                fileType = "XLS";
                            } else if (file.mime.includes("ppt")) {
                                fileIcon = "ph-file-image";
                                fileType = "Image";
                            }
                        }

                        const openUrl = file.mime && file.mime.includes('pdf') ?
                            `/files/${file.id}` :
                            `/file-view/${file.id}`;

                        card.innerHTML = `
                        <div class="mt-3 mx-2 preview-container" style="height: 120px;">
                            <div id="preview-${file.id}" class="d-flex justify-content-center align-items-center h-100 w-100">
                                <i class="ph ${fileIcon} fs-1 text-muted"></i>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center mb-1">
                                <i class="ph ${fileIcon} me-2 text-dark"></i>
                                <span class="fw-semibold text-truncate small" title="${file.name}">${file.name}</span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge bg-secondary rounded-2 px-2"><small>${fileType}</small></span>
                                <span class="text-muted small">${file.size}</span>
                                <div class="dropdown">
                                    <button class="btn btn-link ms-auto text-dark p-0"
                                            data-bs-toggle="dropdown"
                                            data-bs-display="static">
                                        <i class="ph ph-dots-three-vertical fs-6 text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                            href="${openUrl}"
                                            target="_blank">
                                                <i class="ph ph-arrow-up-right fs-5"></i> Open
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                            class="dropdown-item d-flex align-items-center gap-2 download-btn"
                                            data-id="${file.id}"
                                            data-name="${file.name}">
                                            <i class="ph ph-download fs-5"></i> Download
                                            </a>
                                        </li>
                                        <li class="dropdown-submenu position-relative">
                                            <a class="dropdown-item d-flex align-items-center gap-2 info-btn"
                                            href="#"
                                            data-file-id="${file.id}">
                                                <i class="ph ph-info fs-5"></i> Get Info
                                            </a>
                                            <div class="file-info-panel" style="display: none; position: absolute; left: 100%; top: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 10000; border: 1px solid #e9ecef;">
                                                <div class="p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="fw-semibold mb-0">File Information</h6>
                                                        <button type="button" class="btn-close close-info-panel" style="font-size: 0.7rem;"></button>
                                                    </div>

                                                    <div class="text-center mb-3">
                                                        <div class="file-preview-icon mx-auto mb-2">
                                                            <i class="ph ${fileIcon} fs-1 text-muted"></i>
                                                        </div>
                                                        <h6 class="fw-semibold mb-1 text-truncate">${file.name}</h6>
                                                        <small class="text-muted">${fileType} • ${file.size}</small>
                                                    </div>

                                                    <div class="file-details">
                                                        <div class="detail-item mb-2">
                                                            <label class="text-muted small fw-semibold mb-1">Title</label>
                                                            <p class="mb-0 fw-medium text-truncate">${file.name}</p>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="detail-item mb-2">
                                                                    <label class="text-muted small fw-semibold mb-1">Type</label>
                                                                    <p class="mb-0 fw-medium">${fileType}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="detail-item mb-2">
                                                                    <label class="text-muted small fw-semibold mb-1">Size</label>
                                                                    <p class="mb-0 fw-medium">${file.size}</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="detail-item mb-2">
                                                            <label class="text-muted small fw-semibold mb-1">Location</label>
                                                            <p class="mb-0 fw-medium" id="location-${file.id}">Loading...</p>
                                                        </div>

                                                        <div class="detail-item mb-2">
                                                            <label class="text-muted small fw-semibold mb-1">Upload Date</label>
                                                            <p class="mb-0 fw-medium">${file.created_at || 'Unknown'}</p>
                                                        </div>

                                                        <div class="detail-item mb-2">
                                                            <label class="text-muted small fw-semibold mb-1">Owner</label>
                                                            <p class="mb-0 fw-medium">${file.owner === 'me' ? 'You' : 'Shared'}</p>
                                                        </div>

                                                        <div class="detail-item">
                                                            <label class="text-muted small fw-semibold mb-1">Who has access</label>
                                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                                <div class="access-indicator bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                                                    <i class="ph ph-lock text-white" style="font-size: 10px;"></i>
                                                                </div>
                                                                <span class="fw-medium small">Private</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 advanced-share-btn"
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#advancedShareModal"
                                            data-file-id="${file.id}"
                                            data-file-name="${file.name}"
                                            data-file-items="${file.size}"
                                            data-mime="${file.mime || ''}">
                                            <i class="ph ph-share-network fs-5"></i> Share
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                            class="dropdown-item text-danger d-flex align-items-center gap-2 delete-btn"
                                            data-id="${file.id}">
                                            <i class="ph ph-trash fs-5"></i> Delete
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                data-bs-toggle="modal" data-bs-target="#editFileModal">
                                                <i class="ph ph-pencil-simple fs-5"></i> Edit File
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;

                        card.addEventListener('click', (e) => {
                            if (e.target.closest('.dropdown') || e.target.closest(
                                    '.dropdown-menu')) {
                                return;
                            }
                            window.open(openUrl, '_blank');
                        });

                        fileContainer.appendChild(card);

                        if (file.mime && file.mime.includes("pdf") && file.url) {
                            renderPDFPreview(file.url, `preview-${file.id}`, token);
                        }
                    });
                }

            } catch (err) {
                console.error('Error:', err);
                folderContainer.innerHTML = `<p class="text-danger">Gagal memuat data: ${err.message}</p>`;
                fileContainer.innerHTML = `<p class="text-danger">Gagal memuat data: ${err.message}</p>`;
            }
        });

        // Render thumbnail asli untuk semua jenis file
        async function renderFileThumbnail(fileId, mime, token) {
            const wrapper = document.getElementById('file-thumbnail-wrapper');
            const loading = document.getElementById('file-thumbnail-loading');
            wrapper.innerHTML = '';
            loading.classList.remove('d-none');

            try {
                const response = await fetch(`https://pdu-dms.my.id/api/view-file/${fileId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                if (!response.ok) throw new Error('Failed to load file');

                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const type = blob.type;

                let element;

                if (type.startsWith('image/') && !type.includes('svg')) {
                    // GAMBAR: jpg, png, webp, gif
                    element = document.createElement('img');
                    element.src = url;
                    element.className = 'w-100 h-100 object-fit-cover';
                    element.style.borderRadius = '8px';

                } else if (type === 'application/pdf') {
                    // PDF: render halaman pertama
                    const loadingTask = pdfjsLib.getDocument({
                        url
                    });
                    const pdf = await loadingTask.promise;
                    const page = await pdf.getPage(1);
                    const scale = 0.38;
                    const viewport = page.getViewport({
                        scale
                    });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    await page.render({
                        canvasContext: context,
                        viewport
                    }).promise;
                    canvas.style.borderRadius = '8px';
                    element = canvas;

                } else if (type.startsWith('video/')) {
                    // VIDEO
                    element = document.createElement('video');
                    element.src = url;
                    element.poster = ''; // bisa ditambah jika API kasih poster
                    element.className = 'w-100 h-100 object-fit-cover';
                    element.style.borderRadius = '8px';
                    element.muted = true;
                    element.preload = 'metadata';

                } else {
                    throw new Error('No visual preview');
                }

                wrapper.appendChild(element);

            } catch (error) {
                console.warn('Preview tidak tersedia:', error.message);
                // Fallback ke icon
                const iconMap = {
                    'pdf': 'ph-file-pdf',
                    'word': 'ph-file-doc',
                    'excel': 'ph-file-xls',
                    'powerpoint': 'ph-file-ppt',
                    'video': 'ph-file-video',
                    'audio': 'ph-file-audio',
                    'zip': 'ph-file-zip',
                    'default': 'ph-file'
                };

                let icon = iconMap.default;
                if (mime.includes('pdf')) icon = iconMap.pdf;
                else if (mime.includes('word') || mime.includes('document')) icon = iconMap.word;
                else if (mime.includes('excel') || mime.includes('sheet')) icon = iconMap.excel;
                else if (mime.includes('powerpoint')) icon = iconMap.powerpoint;
                else if (mime.includes('video')) icon = iconMap.video;
                else if (mime.includes('audio')) icon = iconMap.audio;
                else if (mime.includes('zip')) icon = iconMap.zip;

                wrapper.innerHTML = `<i class="ph ${icon} fs-1 text-muted"></i>`;
            } finally {
                loading.classList.add('d-none');
                // Cleanup memory
                if (url) setTimeout(() => URL.revokeObjectURL(url), 10000);
            }
        }

        // Modal Share – Update untuk Thumbnail Asli
        async function openAdvancedShareModal(btn) {
            const isFolder = btn.hasAttribute('data-folder-name');
            const isFile = btn.hasAttribute('data-file-name');

            const name = isFolder ? btn.dataset.folderName : btn.dataset.fileName;
            const count = isFolder ? btn.dataset.folderItems : btn.dataset.fileItems;
            const mime = btn.dataset.mime || '';
            const fileId = btn.dataset.fileId || btn.dataset.fileId;

            const folderPreview = document.getElementById('folder-preview');
            const filePreview = document.getElementById('file-preview');

            folderPreview.style.display = 'none';
            filePreview.style.display = 'none';

            if (isFolder) {
                folderPreview.style.display = 'block';
                document.getElementById('preview-title').textContent = name || 'Untitled Folder';
                document.getElementById('preview-title').title = name;
                document.getElementById('preview-subtitle').textContent = count ? `${count} items` : '0 items';

            } else {
                filePreview.style.display = 'block';

                document.getElementById('file-name-display').textContent = name || 'Untitled File';
                document.getElementById('file-name-display').title = name;
                document.getElementById('file-size-display').textContent = count || '—';

                // Badge & icon kecil
                const typeInfo = {
                    text: 'File',
                    icon: 'ph-file'
                };
                if (mime.includes('pdf')) {
                    typeInfo.text = 'PDF';
                    typeInfo.icon = 'ph-file-pdf';
                } else if (mime.includes('word') || mime.includes('document')) {
                    typeInfo.text = 'DOC';
                    typeInfo.icon = 'ph-file-doc';
                } else if (mime.includes('excel') || mime.includes('sheet')) {
                    typeInfo.text = 'XLS';
                    typeInfo.icon = 'ph-file-xls';
                } else if (mime.includes('powerpoint')) {
                    typeInfo.text = 'PPT';
                    typeInfo.icon = 'ph-file-ppt';
                } else if (mime.includes('image')) {
                    typeInfo.text = 'Image';
                    typeInfo.icon = 'ph-file-image';
                } else if (mime.includes('video')) {
                    typeInfo.text = 'Video';
                    typeInfo.icon = 'ph-file-video';
                }

                document.querySelector('#file-badge small').textContent = typeInfo.text;
                document.getElementById('file-small-icon').className = `${typeInfo.icon} me-2 text-dark`;

                // RENDER THUMBNAIL ASLI (PDF, Gambar, Video)
                if (fileId && (mime.includes('pdf') || mime.includes('image/') || mime.includes('video/'))) {
                    await renderFileThumbnail(fileId, mime, "{{ $token }}");
                } else {
                    // Fallback icon
                    document.getElementById('file-thumbnail-wrapper').innerHTML =
                        `<i class="ph ${typeInfo.icon} fs-1 text-muted"></i>`;
                }
            }

            const modal = new bootstrap.Modal(document.getElementById('advancedShareModal'));
            modal.show();
        }

        // Event listener tetap sama
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.advanced-share-btn');
            if (!btn) return;
            e.preventDefault();
            openAdvancedShareModal(btn);
        });

        // Fungsi untuk render PDF preview dengan authentication
        async function renderPDFPreview(pdfUrl, containerId, token) {
            const container = document.getElementById(containerId);

            try {
                container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';

                const loadingTask = pdfjsLib.getDocument({
                    url: pdfUrl,
                    httpHeaders: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1);
                const scale = 0.3;
                const viewport = page.getViewport({
                    scale
                });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height / 1.8;
                canvas.style.maxWidth = '8em';
                canvas.style.height = 'auto';
                canvas.style.borderRadius = '4px';
                canvas.style.padding = '16px 0';

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                await page.render(renderContext).promise;
                container.innerHTML = '';
                container.appendChild(canvas);

            } catch (error) {
                console.error('Error rendering PDF preview:', error);
                container.innerHTML = '<i class="ph ph-file-pdf fs-1 text-muted"></i>';
            }
        }

        // ==================== EVENT LISTENERS UNTUK INFO PANEL ====================

        // Event listener untuk info button (file)
        document.addEventListener("click", function(e) {
            const infoBtn = e.target.closest(".info-btn");
            if (infoBtn) {
                e.preventDefault();
                e.stopPropagation();

                // Sembunyikan semua info panel lainnya
                document.querySelectorAll('.file-info-panel').forEach(panel => {
                    panel.style.display = 'none';
                });

                // Tampilkan info panel yang diklik
                const infoPanel = infoBtn.closest('.dropdown-submenu').querySelector('.file-info-panel');
                infoPanel.style.display = 'block';

                // Dapatkan file ID dan load location data
                const fileId = infoBtn.getAttribute('data-file-id');
                loadFileLocation(fileId, infoPanel);

                // Adjust position jika perlu
                const rect = infoPanel.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    infoPanel.style.left = 'auto';
                    infoPanel.style.right = '100%';
                }
            }
        });

        // Event listener untuk folder info button
        document.addEventListener("click", function(e) {
            const folderInfoBtn = e.target.closest(".folder-info-btn");
            if (folderInfoBtn) {
                e.preventDefault();
                e.stopPropagation();

                // Sembunyikan semua folder info panel lainnya
                document.querySelectorAll('.folder-info-panel').forEach(panel => {
                    panel.style.display = 'none';
                });

                // Tampilkan folder info panel yang diklik
                const folderInfoPanel = folderInfoBtn.closest('.dropdown-submenu').querySelector(
                    '.folder-info-panel');
                folderInfoPanel.style.display = 'block';

                // Dapatkan folder ID dan load location data
                const folderId = folderInfoBtn.getAttribute('data-folder-id');
                loadFolderLocation(folderId, folderInfoPanel);

                // Adjust position jika perlu
                const rect = folderInfoPanel.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    folderInfoPanel.style.left = 'auto';
                    folderInfoPanel.style.right = '100%';
                }
            }
        });

        // Event listener untuk close info panel
        document.addEventListener("click", function(e) {
            const closeBtn = e.target.closest(".close-info-panel");
            if (closeBtn) {
                e.preventDefault();
                e.stopPropagation();
                const infoPanel = closeBtn.closest('.file-info-panel');
                infoPanel.style.display = 'none';
            }
        });

        // Event listener untuk close folder info panel
        document.addEventListener("click", function(e) {
            const closeBtn = e.target.closest(".folder-close-info-panel");
            if (closeBtn) {
                e.preventDefault();
                e.stopPropagation();
                const folderInfoPanel = closeBtn.closest('.folder-info-panel');
                folderInfoPanel.style.display = 'none';
            }
        });

        // Sembunyikan info panel ketika klik di luar
        document.addEventListener("click", function(e) {
            if (!e.target.closest('.dropdown-submenu') && !e.target.closest('.file-info-panel') && !e.target
                .closest('.folder-info-panel')) {
                document.querySelectorAll('.file-info-panel').forEach(panel => {
                    panel.style.display = 'none';
                });
                document.querySelectorAll('.folder-info-panel').forEach(panel => {
                    panel.style.display = 'none';
                });
            }
        });

        // ==================== FUNGSI UNTUK LOAD LOCATION ====================

        // Fungsi untuk load file location (breadcrumb)
        async function loadFileLocation(fileId, infoPanel) {
            const token = "{{ $token }}";
            const locationElement = infoPanel.querySelector(`#location-${fileId}`);

            if (!locationElement) return;

            try {
                // Get file details to get parent_id
                const fileResponse = await fetch(`https://pdu-dms.my.id/api/my-files`, {
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                });

                if (!fileResponse.ok) {
                    throw new Error('Failed to fetch file details');
                }

                const data = await fileResponse.json();
                const files = data.files || [];
                const currentFile = files.find(f => f.id === parseInt(fileId));

                if (!currentFile) {
                    locationElement.textContent = "MySpace";
                    return;
                }

                // Build breadcrumb based on parent_id
                const breadcrumb = await buildFileBreadcrumb(currentFile.parent_id, token);
                locationElement.textContent = breadcrumb;

            } catch (error) {
                console.error('Error loading file location:', error);
                locationElement.textContent = "MySpace";
            }
        }

        // Fungsi untuk load folder location (breadcrumb)
        async function loadFolderLocation(folderId, folderInfoPanel) {
            const token = "{{ $token }}";
            const locationElement = folderInfoPanel.querySelector(`#folder-location-${folderId}`);

            if (!locationElement) return;

            try {
                // Build breadcrumb based on parent_id
                const breadcrumb = await buildFolderBreadcrumb(folderId, token);
                locationElement.textContent = breadcrumb;

            } catch (error) {
                console.error('Error loading folder location:', error);
                locationElement.textContent = "MySpace";
            }
        }

        // Fungsi untuk build breadcrumb dari parent_id (file)
        async function buildFileBreadcrumb(parentId, token) {
            if (!parentId) return "MySpace";

            try {
                const breadcrumb = ["MySpace"];
                let currentId = parentId;

                // Traverse up the folder hierarchy (max 10 levels to avoid infinite loop)
                for (let i = 0; i < 10; i++) {
                    const response = await fetch(`https://pdu-dms.my.id/api/folders/${currentId}`, {
                        headers: {
                            "Authorization": "Bearer " + token
                        }
                    });

                    if (!response.ok) break;

                    const folderData = await response.json();
                    const folder = folderData.folder || folderData;

                    if (folder && folder.name) {
                        breadcrumb.unshift(folder.name); // Add to beginning
                        currentId = folder.parent_id;

                        if (!currentId) break; // Reached root
                    } else {
                        break;
                    }
                }

                return breadcrumb.join(' / ');
            } catch (error) {
                console.error('Error building breadcrumb:', error);
                return "MySpace";
            }
        }

        // Fungsi untuk build folder breadcrumb dari parent_id
        async function buildFolderBreadcrumb(folderId, token) {
            if (!folderId) return "MySpace";

            try {
                const breadcrumb = [];
                let currentId = folderId;

                // Traverse up the folder hierarchy (max 10 levels to avoid infinite loop)
                for (let i = 0; i < 10; i++) {
                    const response = await fetch(`https://pdu-dms.my.id/api/folders/${currentId}`, {
                        headers: {
                            "Authorization": "Bearer " + token
                        }
                    });

                    if (!response.ok) break;

                    const folderData = await response.json();
                    const folder = folderData.folder || folderData;

                    if (folder && folder.name) {
                        breadcrumb.unshift(folder.name); // Add to beginning
                        currentId = folder.parent_id;

                        if (!currentId) {
                            breadcrumb.unshift("MySpace"); // Add MySpace as root
                            break; // Reached root
                        }
                    } else {
                        break;
                    }
                }

                // Jika breadcrumb kosong, tambahkan MySpace
                if (breadcrumb.length === 0) {
                    breadcrumb.push("MySpace");
                }

                return breadcrumb.join(' / ');
            } catch (error) {
                console.error('Error building folder breadcrumb:', error);
                return "MySpace";
            }
        }

        // ==================== EVENT LISTENERS LAINNYA ====================

        // Event listener untuk delete file
        document.addEventListener("click", async function(e) {
            const btn = e.target.closest(".delete-btn");
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const fileId = btn.getAttribute("data-id");
            const token = "{{ $token }}";

            if (!confirm("Yakin mau menghapus file ini?")) return;

            try {
                const payload = {
                    ids: [parseInt(fileId)],
                    parent_id: "",
                    all: ""
                };

                const response = await fetch("https://pdu-dms.my.id/api/delete-file", {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok) {
                    btn.closest(".file-card").remove();
                    alert("File berhasil dihapus");
                } else {
                    alert("Gagal menghapus file: " + (result.message || "Unknown error"));
                }

            } catch (err) {
                console.error(err);
                alert("Gagal menghapus file");
            }
        });

        // Event listener untuk download file
        document.addEventListener("click", async function(e) {
            const btn = e.target.closest(".download-btn");
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const fileId = btn.getAttribute("data-id");
            const fileName = btn.getAttribute("data-name");
            const token = "{{ $token }}";

            if (!token) {
                alert("Token tidak ditemukan. Silakan login ulang.");
                return;
            }

            try {
                const response = await fetch(`https://pdu-dms.my.id/api/view-file/${fileId}`, {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/octet-stream"
                    }
                });

                if (!response.ok) {
                    throw new Error("Gagal mengunduh file. Status: " + response.status);
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement("a");
                a.href = url;
                a.download = fileName || "downloaded_file";
                document.body.appendChild(a);
                a.click();

                a.remove();
                window.URL.revokeObjectURL(url);

                console.log("File berhasil diunduh:", fileName);

            } catch (error) {
                console.error(error);
                alert("Gagal mengunduh file: " + error.message);
            }
        });

        // Event listener untuk delete folder
        document.addEventListener("click", async function(e) {
            const btn = e.target.closest(".folder-delete-btn");
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const folderId = btn.getAttribute("data-id");
            const folderName = btn.getAttribute("data-name");
            const token = "{{ $token }}";

            if (!confirm(`Yakin mau menghapus folder "${folderName}"?`)) return;

            try {
                const payload = {
                    ids: [parseInt(folderId)],
                    parent_id: "",
                    all: ""
                };

                const response = await fetch("https://pdu-dms.my.id/api/delete-file", {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok) {
                    btn.closest(".folder-item").remove();

                    const folderContainer = document.getElementById("folderContainer");
                    const remainingFolders = folderContainer.querySelectorAll('.folder-item');
                    if (remainingFolders.length === 0) {
                        const emptyTemplate = document.getElementById("empty-template").content.cloneNode(true);
                        emptyTemplate.querySelector("i").className = "ph ph-folder-open";
                        emptyTemplate.querySelector("p").textContent = "Create a folder to get organized";
                        folderContainer.appendChild(emptyTemplate);
                    }

                    alert("Folder berhasil dihapus");
                } else {
                    alert("Gagal menghapus folder: " + (result.message || "Unknown error"));
                }

            } catch (err) {
                console.error(err);
                alert("Gagal menghapus folder");
            }
        });

        // Event listener untuk download folder
        document.addEventListener("click", async function(e) {
            const btn = e.target.closest(".folder-download-btn");
            if (!btn) return;

            e.preventDefault();

            const folderId = btn.getAttribute("data-id");
            const folderName = btn.getAttribute("data-name");
            const token = "{{ $token }}";

            if (!token) {
                alert("Token tidak ditemukan. Silakan login ulang.");
                return;
            }

            try {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ph ph-spinner ph-spin fs-5"></i> Downloading...';
                btn.disabled = true;

                const response1 = await fetch("https://pdu-dms.my.id/api/download", {
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        ids: [parseInt(folderId)],
                    })
                });

                const data = await response1.json();
                if (!response1.ok) throw new Error(data.message || "Gagal mendapatkan URL download.");
                if (!data.url) throw new Error("Response tidak mengandung URL download.");

                const fileUrl = data.url;
                const filename = data.filename || folderName + ".zip";

                const response2 = await fetch(fileUrl, {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                });

                if (!response2.ok) throw new Error("Gagal mengunduh file ZIP.");

                const blob = await response2.blob();
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement("a");
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();

                a.remove();
                window.URL.revokeObjectURL(url);

                console.log("Folder berhasil diunduh:", filename);

            } catch (error) {
                console.error(error);
                alert("Gagal mengunduh folder: " + error.message);
            } finally {
                btn.innerHTML = '<i class="ph ph-download fs-5"></i> Download';
                btn.disabled = false;
            }
        });

        // Event listener untuk klik folder card (open folder)
        document.addEventListener("click", function(e) {
            const folderCard = e.target.closest('.folder-card');
            if (folderCard && !e.target.closest('.dropdown') && !e.target.closest('.dropdown-menu')) {
                const folderItem = folderCard.closest('.folder-item');
                const openBtn = folderItem.querySelector('.folder-open-btn');
                if (openBtn) {
                    window.location.href = openBtn.getAttribute('href');
                }
            }
        });
    </script>
@endsection
