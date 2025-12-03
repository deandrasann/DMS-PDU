@extends('layouts.app')

@section('title', 'Shared with Me')

@section('content')
    <div>
        <h4 class="fw-semibold mb-4">Shared with Me</h4>
    </div>
    @if (!empty($breadcrumb) && count($breadcrumb) > 1)
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                @foreach ($breadcrumb as $crumb)
                    @if ($loop->last)
                        <li class="breadcrumb-item active text-dark text-decoration-none" aria-current="page"
                            @if (isset($crumb['id'])) data-id="{{ $crumb['id'] }}" @endif>
                            {{ $crumb['name'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item text-dark"
                            @if (isset($crumb['id'])) data-id="{{ $crumb['id'] }}" @endif>
                            <a href="{{ $crumb['url'] }}" class="text-decoration-none text-dark">
                                {{ $crumb['name'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif
    <div class="container py-2">
        <!-- Shared Folders -->
        <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
            <h4 class="fw-semibold mb-4">Shared Folders</h4>
            <div id="folderContainer" class="row g-3"></div>
        </div>

        <!-- Shared Files -->
        <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
            <h4 class="fw-semibold mb-4">Shared Files</h4>
            <div id="fileContainer" class="row g-3 ms-1 me-2"></div>
        </div>
    </div>

    <!-- Template kosong (sama persis MySpace) -->
    <template id="empty-template">
        <div class="d-flex flex-column grow justify-content-center align-items-center text-center p-5 text-muted">
            <i class="ph ph-folder-open" style="font-size: 80px; color: #9E9E9C;"></i>
            <p class="mt-3">Tidak ada item di sini.</p>
        </div>
    </template>

    <script>
        window.token = "{{ session('token') ?? '' }}";
    window.currentPath = "{{ $currentPath ?? '' }}";
    window.currentFolderName = "{{ $currentFolderName ?? '' }}";

    // Debug
    if (window.token) {
        console.log('Token dari session: ADA (panjang:', window.token.length, ')');
    } else {
        console.warn('Token TIDAK ADA → redirect ke signin');
        alert("Session habis. Silakan login ulang.");
        window.location.href = "/signin";
    }
    </script>


    {{-- Load PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
    </script>

    <script>
        class SharedWithMeManager {
            constructor() {
                this.token = window.token ||'';

                // Ambil currentPath dari PHP
                this.currentPath = "{{ $currentPath }}";
                this.baseUrl = '/shared-with-me';
                this.currentFolderName = "{{ $currentFolderName ?? '' }}";
                if (!this.token) {
                    alert("Session habis. Silakan login ulang.");
                    window.location.href = "/signin";
                    return;
                }

                this.init();
            }

            init() {
                this.updateBreadcrumbFromStorage();
                this.loadItems();
                this.attachGlobalListeners();
            }

            async loadItems() {
                const folderContainer = document.getElementById("folderContainer");
                const fileContainer = document.getElementById("fileContainer");
                const emptyTemplate = document.getElementById("empty-template");

                try {
                    let normalizedFolders = [];
                    let normalizedFiles = [];

                    if (!this.currentPath) {
                        // MODE ROOT: /api/shared-with-me
                        const res = await fetch("https://pdu-dms.my.id/api/shared-with-me", {
                            headers: {
                                "Authorization": "Bearer " + this.token,
                                "Accept": "application/json"
                            }
                        });

                        if (!res.ok) throw new Error("Gagal memuat shared items");

                        const response = await res.json();
                        const items = response.data || response.items || response || [];

                        normalizedFolders = items.filter(i => i.is_folder === true || i.is_folder === 1);
                        normalizedFiles = items.filter(i => !i.is_folder && i.is_folder !== 1);

                    } else {
                        // MODE SUBFOLDER: /api/my-files/{id}
                        const folderId = this.getLastSegmentFromPath();
                        const res = await fetch(`https://pdu-dms.my.id/api/my-files/${folderId}`, {
                            headers: {
                                "Authorization": "Bearer " + this.token,
                                "Accept": "application/json"
                            }
                        });

                        if (!res.ok) throw new Error("Gagal membuka folder");

                        const data = await res.json();

                        // Struktur standar dari /api/my-files
                        normalizedFolders = data.folders || [];
                        normalizedFiles = data.files || [];

                        // // Update breadcrumb dengan nama folder saat ini
                        // this.updateBreadcrumb(data.name || this.getLastSegmentFromPath());
                    }

                    // Normalisasi data biar sama formatnya
                    const folders = normalizedFolders.map(f => ({
                        id: f.id || f.file_id,
                        name: f.name || f.file_name,
                        shared_by_name: f.shared_by?.name || f.shared_by_name || f.owner?.name || 'Someone',
                        size: f.size || '—',
                        created_at: f.created_at || 'Unknown',
                        updated_at: f.updated_at || 'Unknown'
                    }));

                    const files = normalizedFiles.map(f => ({
                        id: f.id || f.file_id,
                        name: f.name || f.file_name,
                        mime: f.mime_type || f.mime || this.guessMime(f.name || f.file_name),
                        labels: f.labels || [],
                        size: f.size || '—',
                        created_at: f.created_at || 'Unknown',
                        updated_at: f.updated_at || 'Unknown',
                        url: f.file_path ? `https://pdu-dms.my.id/storage/${f.file_path}` : null,
                        shared_by_name: f.shared_by?.name || f.shared_by_name || f.owner?.name || 'Someone'
                    }));

                    this.renderFolders(folders, folderContainer, emptyTemplate);
                    this.renderFiles(files, fileContainer, emptyTemplate);

                } catch (err) {
                    console.error(err);
                    folderContainer.innerHTML = `<p class="text-danger text-center">${err.message}</p>`;
                    fileContainer.innerHTML = `<p class="text-danger text-center">${err.message}</p>`;
                }
            }

            getLastSegmentFromPath() {
                const segments = this.currentPath.split('/').filter(seg => seg);
                return segments.length > 0 ? segments[segments.length - 1] : null;
            }

            updateBreadcrumbFromStorage() {
                const breadcrumb = document.querySelector('.breadcrumb');
                if (!breadcrumb) return;

                const folderMapping = JSON.parse(sessionStorage.getItem('folderMapping') || '{}');

                // Simpan nama folder current jika ada
                if (this.currentPath && this.currentFolderName) {
                    const lastSegmentId = this.getLastSegmentFromPath();
                    if (lastSegmentId) {
                        folderMapping[lastSegmentId] = this.currentFolderName;
                        sessionStorage.setItem('folderMapping', JSON.stringify(folderMapping));
                    }
                }

                // Update semua breadcrumb item yang punya data-id
                const items = breadcrumb.querySelectorAll('.breadcrumb-item[data-id]');
                items.forEach(item => {
                    const folderId = item.dataset.id;
                    const folderName = folderMapping[folderId];

                    if (folderName) {
                        // Update text di link atau di span
                        const link = item.querySelector('a');
                        if (link) {
                            link.textContent = folderName;
                        } else {
                            item.textContent = folderName;
                        }
                    }
                });
            }

            // Helper tambahan (hanya ini yang ditambah)
            guessMime(name) {
                const ext = (name || '').toLowerCase().split('.').pop();
                const map = {
                    pdf: "application/pdf",
                    doc: "application/msword",
                    docx: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                    xls: "application/vnd.ms-excel",
                    xlsx: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                };
                return map[ext] || "application/octet-stream";
            }

            // SEMUA FUNGSI DI BAWAH INI TIDAK DIUBAH SAMA SEKALI (100% ORIGINAL)
            renderFolders(folders, container, tmpl) {
                container.innerHTML = '';
                if (folders.length === 0) {
                    const empty = tmpl.content.cloneNode(true);
                    empty.querySelector("i").className = "ph ph-folder-open";
                    empty.querySelector("p").textContent = "No shared folders yet";
                    container.appendChild(empty);
                    return;
                }
                const folderMapping = JSON.parse(sessionStorage.getItem('folderMapping') || '{}');
                folders.forEach(folder => {
                    folderMapping[folder.id] = folder.name;
                    const col = document.createElement("div");
                    col.className = "col-6 col-sm-4 col-md-3 col-lg-2 folder-item";
                    col.innerHTML = this.createFolderHTML(folder);
                    container.appendChild(col);
                });
                sessionStorage.setItem('folderMapping', JSON.stringify(folderMapping));
            }

            createFolderHTML(folder) {
                // Tentukan path baru: currentPath + folder.id
                const newPath = this.currentPath ? `${this.currentPath}/${folder.id}` : folder.id;
                const url = `${this.baseUrl}/${newPath}`;

                return `
        <div class="position-relative folder-item-wrapper">
            <div class="folder-card" style="cursor:pointer; position:relative; overflow:visible !important;" data-folder-id="${folder.id}">
                <img src="/img/folder.svg" alt="Folder" class="img-fluid w-100 h-100 object-fit-contain" style="min-height:100px;">
                
                <div class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between pointer-events-none">
                    <div class="pointer-events-auto">
                        <p class="fw-normal mt-2 mb-0 text-truncate" title="${folder.name}">${folder.name}</p>
                        <small class="fw-light text-muted">Shared by ${folder.shared_by_name || 'Someone'}</small>
                    </div>
                </div>

                <!-- TITIK TIGA TETAP DI DALAM CARD TAPI AKAN DIPORTAL -->
                <div class="position-absolute bottom-0 end-0 mb-2 me-2" style="z-index:10;">
                    <button class="btn btn-link text-dark p-0 dropdown-toggle-portal" 
                            data-folder-id="${folder.id}"
                            data-folder-name="${folder.name}"
                            data-shared-by="${folder.shared_by_name || 'Someone'}"
                            data-folder-size="${folder.size || '—'}"
                            data-created-at="${folder.created_at || 'Unknown'}"
                            data-updated-at="${folder.updated_at || 'Unknown'}">
                        <i class="ph ph-dots-three-vertical fs-5 text-muted"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
            }

            renderFiles(files, container, tmpl) {
                container.innerHTML = '';
                if (files.length === 0) {
                    const empty = tmpl.content.cloneNode(true);
                    empty.querySelector("i").className = "ph ph-file";
                    empty.querySelector("p").textContent = "No shared files yet";
                    container.appendChild(empty);
                    return;
                }
                files.forEach(file => {
                    const card = this.createFileCard(file);
                    container.appendChild(card);
                });
            }

            createFileCard(file) {
                const card = document.createElement("div");
                card.className = "card rounded-4 border-dark-subtle border-1 me-3 file-card";
                card.style.width = "160px";
                card.style.height = "180px";
                card.style.backgroundColor = "#F2F2F0";
                card.style.cursor = "pointer";

                const {
                    icon
                } = this.getFileIcon(file.mime);
                const openUrl = file.mime?.includes('pdf') ? `/files/${file.id}` : `/file-view/${file.id}`;

                const labelsHTML = this.createLabelsHTML(file.labels || []);

                card.innerHTML = `
            <div class="mt-3 mx-2 preview-container" style="height:100px;display:flex;align-items:center;justify-content:center;">
                <div id="preview-${file.id}" class="d-flex justify-content-center align-items-center w-100 h-100">
                    <i class="ph ${icon} fs-1 text-muted"></i>
                </div>
            </div>
            <div class="card-body p-2 d-flex flex-column">
                <div class="d-flex align-items-center mb-1">
                    <i class="ph ${icon} me-2 text-dark"></i>
                    <span class="fw-semibold text-truncate small" title="${file.name}">${file.name}</span>
                </div>
                <div class="d-flex align-items-start justify-content-between flex-grow-1">
                    <div class="labels-section me-2">${labelsHTML}</div>
                    <div class="dropdown">
                        <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                            <i class="ph ph-dots-three-vertical fs-6 text-muted"></i>
                        </button>
                        <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="${openUrl}" target="_blank">
                                <i class="ph ph-arrow-up-right fs-5"></i> Open
                            </a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 download-btn" href="#" data-id="${file.id}" data-name="${file.name}">
                                <i class="ph ph-download fs-5"></i> Download
                            </a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2 info-btn" href="#" data-file='${JSON.stringify(file)}' data-shared-by="${file.shared_by_name || 'Someone'}">
                                <i class="ph ph-info fs-5"></i> Get Info
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;

                card.addEventListener('click', e => {
                    if (e.target.closest('.dropdown') || e.target.closest('.dropdown-menu')) return;
                    window.open(openUrl, '_blank');
                });

                if (file.mime?.includes('pdf') && file.url) {
                    setTimeout(() => this.renderPDFPreview(file.url, `preview-${file.id}`), 100);
                }

                return card;
            }

            getFileIcon(mime) {
                if (!mime) return {
                    icon: "ph-file"
                };
                if (mime.includes("pdf")) return {
                    icon: "ph-file-pdf"
                };
                if (mime.includes("doc") || mime.includes("document")) return {
                    icon: "ph-file-doc"
                };
                if (mime.includes("xls") || mime.includes("sheet")) return {
                    icon: "ph-file-xls"
                };
                return {
                    icon: "ph-file"
                };
            }

            createLabelsHTML(labels) {
                if (!labels || labels.length === 0)
                    return '<span class="badge bg-secondary rounded-2 px-2"><small>File</small></span>';
                return labels.map(l => `
            <span class="badge rounded-2 px-2 mb-1" style="background:#${l.color};color:${this.getTextColor(l.color)}">
                ${l.name}
            </span>
        `).join('');
            }

            getTextColor(bg) {
                const map = {
                    "FDDCD9": "#CB564A",
                    "EBE0D9": "#763E1A",
                    "FDE9DD": "#C2825D",
                    "EFEAFF": "#7762BB",
                    "FCF9DE": "#BDB470",
                    "E4F3FE": "#5F92B6",
                    "FCE7ED": "#CA8499",
                    "E6E5E3": "#989797",
                    "EEFEF1": "#8ABB93",
                    "F0EFED": "#729D9C"
                };
                return map[bg?.toUpperCase()] || "#000";
            }

            async renderPDFPreview(url, containerId) {
                const container = document.getElementById(containerId);
                if (!container) return;
                try {
                    container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
                    const loadingTask = pdfjsLib.getDocument({
                        url,
                        httpHeaders: {
                            Authorization: "Bearer " + this.token
                        }
                    });
                    const pdf = await loadingTask.promise;
                    const page = await pdf.getPage(1);
                    const scale = Math.min(120 / page.getViewport({
                        scale: 1
                    }).width, 1.5);
                    const viewport = page.getViewport({
                        scale
                    });
                    const canvas = document.createElement("canvas");
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    canvas.style.width = "100%";
                    canvas.style.height = "100%";
                    await page.render({
                        canvasContext: canvas.getContext("2d"),
                        viewport
                    }).promise;
                    container.innerHTML = '';
                    container.appendChild(canvas);
                } catch (e) {
                    container.innerHTML = '<i class="ph ph-file-pdf fs-1 text-muted"></i>';
                }
            }

            attachGlobalListeners() {
                // Klik pada folder card (untuk membuka folder di shared-with-me)
                document.addEventListener('click', (e) => {
                    const folderCard = e.target.closest('.folder-card');
                    if (folderCard) {
                        e.preventDefault();
                        const folderId = folderCard.dataset.folderId;
                        const newPath = this.currentPath ? `${this.currentPath}/${folderId}` : folderId;
                        window.location.href = `${this.baseUrl}/${newPath}`;
                    }
                });

                document.addEventListener("click", async e => {
                    const btn = e.target.closest(".download-btn");
                    if (!btn) return;
                    e.preventDefault();
                    e.stopPropagation();
                    const id = btn.dataset.id,
                        name = btn.dataset.name;
                    try {
                        const res = await fetch(`https://pdu-dms.my.id/api/view-file/${id}`, {
                            headers: {
                                Authorization: "Bearer " + this.token
                            }
                        });
                        const blob = await res.blob();
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement("a");
                        a.href = url;
                        a.download = name;
                        a.click();
                        URL.revokeObjectURL(url);
                    } catch (err) {
                        alert("Download gagal");
                    }
                });

                // === PORTAL DROPDOWN FOLDER
                let activePortal = null;

                document.addEventListener("click", e => {
                    const btn = e.target.closest(".dropdown-toggle-portal");
                    if (!btn) {
                        // Klik di luar = tutup portal
                        if (activePortal) {
                            activePortal.remove();
                            activePortal = null;
                        }
                        return;
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    // Tutup yang lama
                    if (activePortal) activePortal.remove();

                    const rect = btn.getBoundingClientRect();

                    // Buat dropdown portal di body
                    const portal = document.createElement("div");
                    portal.className = "dropdown-portal shadow-lg border-0 rounded-3";
                    portal.style.position = "fixed";
                    portal.style.top = `${rect.bottom + 8}px`;
                    portal.style.left = `${rect.left}px`;
                    portal.style.zIndex = "9999";
                    portal.style.background = "rgba(255, 255, 255, 0.92)"; // transparan
                    portal.style.backdropFilter = "blur(12px)";
                    portal.style.minWidth = "150px";
                    portal.style.overflow = "hidden";
                    // Link Open mengarah ke shared-with-me
                    const newPath = this.currentPath ?
                        `${this.currentPath}/${btn.dataset.folderId}` :
                        btn.dataset.folderId;

                    portal.innerHTML = `
            <ul class="dropdown-menu shadow show p-2 m-0" style="position:relative; box-shadow: 0 10px 40px rgba(0,0,0,0.3); border: none;">
                <li><a class="dropdown-item d-flex align-items-center gap-2 rounded-2" href="${this.baseUrl}/${newPath}">
                    <i class="ph ph-arrow-up-right fs-5"></i> Open
                </a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2 rounded-2 folder-portal-info" href="#">
                    <i class="ph ph-info fs-5"></i> Get Info
                </a></li>
            </ul>
        `;

                    document.body.appendChild(portal);
                    activePortal = portal;

                    // Get Info di portal — VERSI 100% SAMA DENGAN MYSPACE
                    portal.querySelector(".folder-portal-info").addEventListener("click", ev => {
                        ev.preventDefault();

                        // Cegah buka dua kali
                        if (document.querySelector(".info-portal")) return;

                        // Ambil data folder dari button
                        const folder = {
                            id: btn.dataset.folderId,
                            name: btn.dataset.folderName,
                            shared_by_name: btn.dataset.sharedBy,
                            size: btn.dataset.folderSize || "—", // kalau ada size dari API
                            created_at: btn.dataset.createdAt || "Unknown",
                            updated_at: btn.dataset.updatedAt || "Unknown"
                        };

                        const info = document.createElement("div");
                        info.className = "info-portal shadow-lg bg-white rounded-4 border-0";
                        info.style.position = "fixed";
                        info.style.top = "60px";
                        info.style.right = "20px";
                        info.style.width = "250px";
                        info.style.maxHeight = "90vh";
                        info.style.overflowY = "auto";
                        info.style.zIndex = "10000";
                        info.style.fontSize = "0.925rem";

                        info.innerHTML = `
                            <div class="p-3" style="background: linear-gradient(to bottom, #f8f9fa, #ffffff); border-radius: 16px 16px 0 0;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-semibold mb-0">Folder Information</h6>
                                    <button type="button" class="btn-close btn-close-portal" style="font-size: 0.7rem; opacity: 0.6;"></button>
                                </div>
                            </div>

                            <div class="p-3 pt-0">
                                <div class="detail-item mb-3">
                                    <label class="text-muted small fw-semibold mb-1 d-block">Folder Name</label>
                                    <p class="mb-0 fw-medium text-truncate">${folder.name}</p>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted small fw-semibold mb-1 d-block">Type</label>
                                    <p class="mb-0 fw-medium">Folder</p>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted small fw-semibold mb-1 d-block">Size</label>
                                    <p class="mb-0 fw-medium">${folder.size}</p>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted small fw-semibold mb-1 d-block">Created Date</label>
                                    <p class="mb-0 fw-medium text-secondary small">${folder.created_at}</p>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted small fw-semibold mb-1 d-block">Owner</label>
                                    <p class="mb-0 fw-medium">${folder.shared_by_name}</p>
                                </div>

                                <div class="detail-item">
                                    <label class="text-muted small fw-semibold mb-2 d-block">Who has access</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 32px; height: 32px;">
                                            <i class="ph ph-users text-primary" style="font-size: 16px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium small">Shared with you</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">by ${folder.shared_by_name}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        document.body.appendChild(info);

                        // Tutup panel
                        info.querySelector(".btn-close-portal").onclick = () => info.remove();
                    });
                });

                document.addEventListener("click", e => {
                    const btn = e.target.closest(".info-btn");
                    if (!btn) return;
                    e.preventDefault();

                    // Cegah buka dua kali
                    if (document.querySelector(".info-portal")) return;

                    let file;
                    try {
                        file = JSON.parse(btn.dataset.file);
                    } catch {
                        file = btn.dataset.file || {};
                    }

                    const sharedBy = btn.dataset.sharedBy || file.shared_by?.name || file.shared_by_name ||
                        'Someone';
                    const fileType = file.mime?.includes('pdf') ? 'PDF' :
                        file.mime?.includes('doc') ? 'DOC' :
                        file.mime?.includes('xls') ? 'XLSX' :
                        file.mime?.includes('image') ? 'Image' : 'File';

                    const info = document.createElement("div");
                    info.className = "info-portal shadow-lg bg-white rounded-4 border-0";
                    info.style.position = "fixed";
                    info.style.top = "60px";
                    info.style.right = "20px";
                    info.style.width = "250px";
                    info.style.maxHeight = "90vh";
                    info.style.overflowY = "auto";
                    info.style.zIndex = "10000";
                    info.style.fontSize = "0.925rem";

                    info.innerHTML = `
                    <div class="p-3" style="background: linear-gradient(to bottom, #f8f9fa, #ffffff); border-radius: 16px 16px 0 0;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0">File Information</h6>
                            <button type="button" class="btn-close btn-close-portal" style="font-size: 0.7rem; opacity: 0.6;"></button>
                        </div>
                    </div>

                    <div class="p-3 pt-0">
                        <div class="detail-item mb-3">
                            <label class="text-muted small fw-semibold mb-1 d-block">File Name</label>
                            <p class="mb-0 fw-medium text-truncate">${file.name}</p>
                        </div>
                        <div class="detail-item mb-3">
                            <label class="text-muted small fw-semibold mb-1 d-block">Type</label>
                            <p class="mb-0 fw-medium">${fileType}</p>
                        </div>
                        <div class="detail-item mb-3">
                            <label class="text-muted small fw-semibold mb-1 d-block">Size</label>
                            <p class="mb-0 fw-medium">${file.size || '—'}</p>
                        </div>
                        <div class="detail-item mb-3">
                            <label class="text-muted small fw-semibold mb-1 d-block">Created Date</label>
                            <p class="mb-0 fw-medium text-secondary small">
                                ${file.created_at ? new Date(file.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : 'Unknown'}
                            </p>
                        </div>
                        <div class="detail-item mb-3">
                            <label class="text-muted small fw-semibold mb-1 d-block">Owner</label>
                            <p class="mb-0 fw-medium">${sharedBy}</p>
                        </div>

                        <div class="detail-item">
                            <label class="text-muted small fw-semibold mb-2 d-block">Who has access</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                    style="width: 32px; height: 32px;">
                                    <i class="ph ph-users text-primary" style="font-size: 16px;"></i>
                                </div>
                                <div>
                                    <div class="fw-medium small">Shared with you</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">by ${sharedBy}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    document.body.appendChild(info);

                    // Tutup panel
                    info.querySelector(".btn-close-portal").onclick = () => info.remove();
                });
                // Tutup panel kalau klik di luar
                document.addEventListener("click", e => {
                    if (!e.target.closest(".dropdown-menu") && !e.target.closest(".dropdown")) {
                        document.querySelectorAll(".info-panel").forEach(p => p.style.display = "none");
                    }
                });

                // Close button di panel
                document.addEventListener("click", e => {
                    const close = e.target.closest(".close-info-panel");
                    if (close) {
                        close.closest(".info-panel").style.display = "none";
                    }
                });
            }

            handleUnauthorized() {
                alert("Session habis. Silakan login ulang.");
                window.location.href = "/signin";
            }
        }
        // // === FUNGSI GLOBAL: BUKA FOLDER SHARED (PAKAI API YANG SUDAH ADA) ===
        // function openSharedFolder(folderId) {
        //     // Simpan ID folder yang mau dibuka
        //     sessionStorage.setItem('open_shared_folder_id', folderId);
        //     // Redirect ke MySpace
        //     window.location.href = '/myspace';
        // }

        // // === DI MYSPACE: OTOMATIS BUKA FOLDER SHARED PAKAI API /api/my-files/{id} ===
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Hanya jalankan di halaman MySpace
        //     if (window.location.pathname !== '/myspace' && !window.location.pathname.startsWith('/myspace/'))
        //         return;

        //     const sharedFolderId = sessionStorage.getItem('open_shared_folder_id');
        //     if (!sharedFolderId) return;

        //     // Hapus biar tidak keulang
        //     sessionStorage.removeItem('open_shared_folder_id');

        //     // Pastikan MySpaceManager sudah siap
        //     const waitForManager = setInterval(() => {
        //         if (window.mySpaceManager && typeof window.mySpaceManager.loadFilesAndFolders ===
        //             'function') {
        //             clearInterval(waitForManager);

        //             // Ganti currentPath langsung ke folder yang dishare
        //             window.mySpaceManager.currentPath = sharedFolderId;

        //             // Reset URL biar breadcrumb benar
        //             const newUrl = `/myspace/${sharedFolderId}`;
        //             history.replaceState({}, '', newUrl);

        //             // Load isi folder pakai API yang sudah ada: /api/my-files/{id}
        //             window.mySpaceManager.loadFilesAndFolders();

        //             // Optional: kasih tahu user
        //             setTimeout(() => {
        //                 const toast = document.createElement('div');
        //                 toast.className = 'position-fixed top-0 start-50 translate-middle-x p-3';
        //                 toast.style.zIndex = '9999';
        //                 toast.innerHTML = `
    //             <div class="alert alert-success alert-dismissible fade show shadow">
    //                 Berhasil membuka folder yang dishare!
    //                 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    //             </div>
    //         `;
        //                 document.body.appendChild(toast);
        //                 setTimeout(() => toast.remove(), 4000);
        //             }, 1500);
        //         }
        //     }, 100);

        //     // Kalau setelah 5 detik masih belum ada manager → fallback
        //     setTimeout(() => clearInterval(waitForManager), 5000);
        // });
        document.addEventListener("DOMContentLoaded", () => {
            new SharedWithMeManager();
        });
    </script>
@endsection
