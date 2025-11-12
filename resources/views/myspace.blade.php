@extends('layouts.app')

@section('title', 'My Space')

@section('content')
@php
    $segments = array_filter(explode('/', $currentPath));
    $accum = '';
@endphp
@if (!empty($breadcrumb) && request()->path() !== 'myspace')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            @foreach ($breadcrumb as $crumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active text-dark">
                        {{ $crumb['name'] }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ url('myspace/' . $crumb['id']) }}" class="text-dark text-decoration-none">
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
        <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4 z-3" style="z-index: 9999 !important;">
            <h4 class="fw-semibold mb-4">My Folders</h4>
            <div id="folderContainer" class="row g-3"></div>
        </div>

        {{-- ========== SECTION: MY FILES ========== --}}
        <div class="d-flex flex-column shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4 z-n1">
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
            const currentPath = "{{ $currentPath }}";
            const emptyTemplate = document.getElementById("empty-template").content.cloneNode(true);

            try {

                const baseUrl = "http://pdu-dms.my.id/api/my-files";
                const url = currentPath ? `${baseUrl}/${currentPath}` : baseUrl;

                const response = await fetch(url, {
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                });


                if (!response.ok) throw new Error("Gagal memuat data");
                const data = await response.json();

                const folders = data.files.filter(f => f.is_folder);
                const files = data.files.filter(f => !f.is_folder);

                // ===== Render Folders =====
                // ===== Render Folders =====
if (folders.length === 0) {
    const empty = emptyTemplate.cloneNode(true);
    empty.querySelector("i").className = "ph ph-folder-open";
    empty.querySelector("p").textContent = "Create a folder to get organized";
    folderContainer.appendChild(empty);
} else {
    folders.forEach(folder => {
        const col = document.createElement("div");
        col.className = "col-6 col-sm-4 col-md-3 col-lg-2 folder-item";
        col.innerHTML = `
            <div class="position-relative">
                <div class="folder-card" style="cursor: pointer;">
                    <img src="{{ asset('storage/images/folder.svg') }}" alt="Folder" class="img-fluid w-100 h-100 object-fit-contain" style="min-height: 100px; min-width:120px">
                    <div class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <p class="fw-normal mt-2 mb-0 text-truncate" title="${folder.name}">${folder.name}</p>
                            <small class="fw-light">${folder.size}</small>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-link ms-auto text-dark p-0 folder-dropdown-btn"
                                    data-folder-id="${folder.id}"
                                    data-folder-name="${folder.name}">
                                <i class="ph ph-dots-three-vertical fs-5 text-muted"></i>
                            </button>
                            <ul class="dropdown-menu folder-dropdown-menu" style="z-index:9999 !important">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 folder-open-btn"
                                       href="/myspace/${folder.id}">
                                        <i class="ph ph-arrow-up-right fs-5"></i> Open
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
                        card.style.cursor = "pointer"; // Tambahkan cursor pointer

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

                        // Tentukan URL untuk membuka file
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
        <div class="dropdown z-3">
            <button class="btn btn-link ms-auto text-dark p-0 z-3"
                    data-bs-toggle="dropdown"
                    data-bs-display="static">
                <i class="ph ph-dots-three-vertical fs-6 text-muted z-3"></i>
            </button>
            <ul class="dropdown-menu shadow rounded-3 border-0 p-2 z-3">
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

                        // Tambahkan event listener untuk klik card
                        card.addEventListener('click', (e) => {
                            // Cegah event bubbling jika yang diklik adalah dropdown
                            if (e.target.closest('.dropdown') || e.target.closest(
                                    '.dropdown-menu')) {
                                return;
                            }
                            window.open(openUrl, '_blank');
                        });

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
                canvas.height = viewport.height / 1.8;
                canvas.style.maxWidth = '8em';
                canvas.style.height = 'auto';
                canvas.style.borderRadius = '4px';
                canvas.style.padding = '16px 0';

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
        document.addEventListener("click", async function(e) {
            const btn = e.target.closest(".delete-btn");
            if (!btn) return;

            e.preventDefault();
            const fileId = btn.getAttribute("data-id");
            const token = localStorage.getItem("token");

            if (!confirm("Yakin mau hapus file ini?")) return;

            try {
                const response = await fetch("http://pdu-dms.my.id/api/delete-file", {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer " + token,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        all: "",
                        ids: [parseInt(fileId)]
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    btn.closest(".file-card").remove(); // hapus dari tampilan
                } else {
                    alert("Gagal menghapus: " + (result.message || "Unknown error"));
                }

            } catch (err) {
                console.error(err);
            }
        });

        document.addEventListener("click", async function(e) {
            const btn = e.target.closest(".download-btn");
            if (!btn) return;

            e.preventDefault();

            const fileId = btn.getAttribute("data-id");
            const fileName = btn.getAttribute("data-name");
            const token = localStorage.getItem("token");

            if (!token) {
                alert("Token tidak ditemukan. Silakan login ulang.");
                return;
            }

            try {
                const response = await fetch(`http://pdu-dms.my.id/api/view-file/${fileId}`, {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "Accept": "application/octet-stream"
                    }
                });

                if (!response.ok) {
                    throw new Error("Gagal mengunduh file. Status: " + response.status);
                }

                // Konversi ke blob untuk bisa diunduh
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                // Buat link download sementara
                const a = document.createElement("a");
                a.href = url;
                a.download = fileName || "downloaded_file";
                document.body.appendChild(a);
                a.click();

                // Bersihkan URL sementara
                a.remove();
                window.URL.revokeObjectURL(url);

                console.log("File berhasil diunduh:", fileName);

            } catch (error) {
                console.error(error);
                alert("Gagal mengunduh file: " + error.message);
            }
        });
        // Event listener untuk dropdown folder
document.addEventListener("click", function(e) {
    const btn = e.target.closest(".folder-dropdown-btn");
    if (btn) {
        e.preventDefault();
        e.stopPropagation();

        // Sembunyikan semua dropdown lainnya
        document.querySelectorAll('.folder-dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });

        // Tampilkan dropdown yang diklik
        const dropdown = btn.nextElementSibling;
        dropdown.classList.add('show');
    } else {
        // Sembunyikan semua dropdown ketika klik di luar
        document.querySelectorAll('.folder-dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Event listener untuk klik folder card (open folder)
document.addEventListener("click", function(e) {
    const folderCard = e.target.closest('.folder-card');
    if (folderCard && !e.target.closest('.folder-dropdown-btn')) {
        const folderItem = folderCard.closest('.folder-item');
        const openBtn = folderItem.querySelector('.folder-open-btn');
        if (openBtn) {
            window.location.href = openBtn.getAttribute('href');
        }
    }
});

// Event listener untuk delete folder
document.addEventListener("click", async function(e) {
    const btn = e.target.closest(".folder-delete-btn");
    if (!btn) return;

    e.preventDefault();
    const folderId = btn.getAttribute("data-id");
    const folderName = btn.getAttribute("data-name");
    const token = localStorage.getItem("token");

    if (!confirm(`Yakin mau menghapus folder "${folderName}"?`)) return;

    try {
        const response = await fetch("http://pdu-dms.my.id/api/delete-folder", {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                ids: [parseInt(folderId)]
            })
        });

        const result = await response.json();

        if (response.ok) {
            // Hapus folder dari tampilan
            btn.closest(".folder-item").remove();

            // Jika tidak ada folder lagi, tampilkan empty state
            const folderContainer = document.getElementById("folderContainer");
            const remainingFolders = folderContainer.querySelectorAll('.folder-item');
            if (remainingFolders.length === 0) {
                const empty = emptyTemplate.cloneNode(true);
                empty.querySelector("i").className = "ph ph-folder-open";
                empty.querySelector("p").textContent = "Create a folder to get organized";
                folderContainer.appendChild(empty);
            }
        } else {
            alert("Gagal menghapus folder: " + (result.message || "Unknown error"));
        }

    } catch (err) {
        console.error(err);
        alert("Gagal menghapus folder");
    }
});

// Event listener untuk download folder
document.addEventListener("click", async function (e) {
    const btn = e.target.closest(".folder-download-btn");
    if (!btn) return;

    e.preventDefault();

    const folderId = btn.getAttribute("data-id");
    const folderName = btn.getAttribute("data-name");
    const token = localStorage.getItem("token");

    if (!token) {
        alert("Token tidak ditemukan. Silakan login ulang.");
        return;
    }

    try {
        // Tampilkan indikator loading
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="ph ph-spinner ph-spin fs-5"></i> Downloading...';
        btn.disabled = true;

        // === STEP 1: Panggil endpoint /api/download untuk dapatkan URL zip ===
        const response1 = await fetch("http://pdu-dms.my.id/api/download", {
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

        // === STEP 2: Download file aktual dari URL ===
        const response2 = await fetch(fileUrl, {
            method: "GET",
            headers: {
                "Authorization": "Bearer " + token
            }
        });

        if (!response2.ok) throw new Error("Gagal mengunduh file ZIP.");

        const blob = await response2.blob();
        const url = window.URL.createObjectURL(blob);

        // Buat link download sementara
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


    </script>

@endsection

