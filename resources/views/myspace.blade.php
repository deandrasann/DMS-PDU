@extends('layouts.app')

@section('title', 'My Space')

@section('content')
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

        document.addEventListener("DOMContentLoaded", async () => {
            const folderContainer = document.getElementById("folderContainer");
            const fileContainer = document.getElementById("fileContainer");
            const token = localStorage.getItem("token");
            const emptyTemplate = document.getElementById("empty-template").content.cloneNode(true);

            try {
                const response = await fetch("http://pdu-dms.my.id/api/my-files/", {
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                });

                if (!response.ok) throw new Error("Gagal memuat data");
                const data = await response.json();

                const folders = data.files.filter(f => f.is_folder);
                const files = data.files.filter(f => !f.is_folder);

                // ===== Render Folders =====
                if (folders.length === 0) {
                    const empty = emptyTemplate.cloneNode(true);
                    empty.querySelector("i").className = "ph ph-folder-open";
                    empty.querySelector("p").textContent = "Create a folder to get organized";
                    folderContainer.appendChild(empty);
                } else {
                    folders.forEach(folder => {
                        const col = document.createElement("div");
                        col.className = "col-6 col-sm-4 col-md-3 col-lg-2";
                        col.innerHTML = `
                    <div class="position-relative">
                        <img src="{{ asset('storage/images/folder.svg') }}" alt="Folder" class="img-fluid w-100 h-100 object-fit-contain" style="min-height: 100px; min-width:120px">
                        <div class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <p class="fw-normal mt-2 mb-0">${folder.name}</p>
                                <small class="fw-light">${folder.size}</small>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-link ms-auto text-dark p-0" data-bs-toggle="dropdown">
                                    <i class="ph ph-dots-three-vertical fs-5 text-muted"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Download</a></li>
                                    <li><a class="dropdown-item" href="#">Share</a></li>
                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                        folderContainer.appendChild(col);
                    });
                }

                // ===== Render Files =====
                if (files.length === 0) {
                    const empty = emptyTemplate.cloneNode(true);
                    empty.querySelector("i").className = "ph ph-file";
                    empty.querySelector("p").textContent = "Upload your first file to begin";
                    fileContainer.appendChild(empty);
                } else {
                    // Render semua file terlebih dahulu
                    files.forEach(file => {
                        const card = document.createElement("div");
                        card.className = "card rounded-4 border-dark-subtle border-1 me-3 file-card";
                        card.style.width = "180px";
                        card.style.height = "220px";
                        card.style.backgroundColor = "#F2F2F0";

                        // Tentukan ikon berdasarkan tipe file
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
                            <button class="btn btn-link ms-auto text-dark p-0" data-bs-toggle="dropdown">
                                <i class="ph ph-dots-three-vertical fs-6 text-muted"></i>
                            </button>
                            <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
                            <li>
                            <a class="dropdown-item d-flex align-items-center gap-2"
                                href="${file.mime && file.mime.includes('pdf')
                                ? `/files/${file.id}`
                                : `/file-view/${file.id}`}"
                                target="_blank">
                                <i class="ph ph-arrow-up-right fs-5"></i> Open
                            </a>
                            </li>

                                <li><a class="dropdown-item d-flex align-items-center gap-2" href="${file.url}" download><i class="ph ph-download fs-5"></i> Download</a></li>
                                <li><a class="dropdown-item text-danger d-flex align-items-center gap-2" href="#"><i class="ph ph-trash fs-5"></i> Delete</a></li>
                            </ul>
                        </div>
                    </div>
                `;

                        fileContainer.appendChild(card);

                        // Jika file PDF, render preview-nya
                        if (file.mime && file.mime.includes("pdf") && file.url) {
                            renderPDFPreview(file.url, `preview-${file.id}`, token);
                        }
                    });
                }

            } catch (err) {
                console.error(err);
                folderContainer.innerHTML = `<p class="text-danger">Gagal memuat data.</p>`;
                fileContainer.innerHTML = `<p class="text-danger">Gagal memuat data.</p>`;
            }
        });

        // Fungsi untuk render PDF preview dengan authentication
        async function renderPDFPreview(pdfUrl, containerId, token) {
            const container = document.getElementById(containerId);

            try {
                // Tambahkan loading indicator
                container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';

                // Load PDF dengan headers authorization
                const loadingTask = pdfjsLib.getDocument({
                    url: pdfUrl,
                    httpHeaders: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                const pdf = await loadingTask.promise;

                // Get first page
                const page = await pdf.getPage(1);

                // Set scale untuk thumbnail
                const scale = 0.3; // Scale lebih kecil untuk thumbnail
                const viewport = page.getViewport({
                    scale
                });

                // Create canvas
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height / 1.5;
                canvas.style.maxWidth = '100%';
                canvas.style.height = 'auto';
                canvas.style.borderRadius = '4px';

                // Render PDF page to canvas
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                await page.render(renderContext).promise;

                // Clear container dan tambahkan canvas
                container.innerHTML = '';
                container.appendChild(canvas);

            } catch (error) {
                console.error('Error rendering PDF preview:', error);
                // Jika gagal, tetap tampilkan ikon PDF
                container.innerHTML = '<i class="ph ph-file-pdf fs-1 text-muted"></i>';
            }
        }
    </script>

    </style>
@endsection
