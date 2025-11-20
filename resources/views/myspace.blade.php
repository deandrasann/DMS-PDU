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
            folderContainer.innerHTML = `<p class="text-danger">Token tidak tersedia. Silakan login ulang.</p>`;
            fileContainer.innerHTML = `<p class="text-danger">Token tidak tersedia. Silakan login ulang.</p>`;
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
                                                    <div class="folder-info-panel" style="display: none; position: absolute; left: 100%; top: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border: 1px solid #e9ecef;">
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
                        } else if (file.mime.includes("xlsx") || file.mime.includes("spreadsheet")) {
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
                                            <div class="file-info-panel" style="display: none; position: absolute; left: 100%; top: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border: 1px solid #e9ecef;">
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
                                        <a class="dropdown-item d-flex align-items-center gap-2 edit-file-btn" href="#"
                                        data-id="${file.id}" data-name="${file.name}" data-labels='${JSON.stringify(file.labels)}'>
                                            <i class="ph ph-pencil-simple fs-5"></i> Edit File
                                        </a>
                                    </li>
                                        <li>
                                            <a href="#"
                                            class="dropdown-item text-danger d-flex align-items-center gap-2 delete-btn"
                                            data-id="${file.id}">
                                            <i class="ph ph-trash fs-5"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;

                    card.addEventListener('click', (e) => {
                        if (e.target.closest('.dropdown') || e.target.closest('.dropdown-menu')) {
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

            // Initialize edit functionality setelah files dirender
            initializeEditFunctionality();

        } catch (err) {
            console.error('Error:', err);
            folderContainer.innerHTML = `<p class="text-danger">Gagal memuat data: ${err.message}</p>`;
            fileContainer.innerHTML = `<p class="text-danger">Gagal memuat data: ${err.message}</p>`;
        }
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
            const folderInfoPanel = folderInfoBtn.closest('.dropdown-submenu').querySelector('.folder-info-panel');
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
        if (!e.target.closest('.dropdown-submenu') && !e.target.closest('.file-info-panel') && !e.target.closest('.folder-info-panel')) {
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
