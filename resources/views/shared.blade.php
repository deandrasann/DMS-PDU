{{-- resources/views/shared-with-me.blade.php --}}
@extends('layouts.app')

@section('title', 'Shared with Me')

@section('content')
<div>
    <h4 class="fw-semibold mb-4">Shared with Me</h4>
</div>

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

{{-- Token pasti ada --}}
@auth
    @if(empty(auth()->user()->api_token))
        @php
            auth()->user()->update(['api_token' => \Illuminate\Support\Str::random(60)]);
        @endphp
    @endif
    <meta name="api-token" content="{{ auth()->user()->api_token }}">
@endauth

<script>
class SharedWithMeManager {
    constructor() {
    // Cara ini PASTI berhasil karena sama persis seperti MySpace
    this.token = window.token ||
                 (window.Laravel?.apiToken) ||
                 localStorage.getItem('token') ||
                 document.querySelector('meta[name="api-token"]')?.getAttribute('content') ||
                 '';

    if (!this.token) {
        alert("Session habis. Silakan login ulang.");
        window.location.href = "/signin";
        return;
    }
    this.init();
}

    init() {
        this.loadSharedItems();
        this.attachGlobalListeners();
    }

    async loadSharedItems() {
        const folderContainer = document.getElementById("folderContainer");
        const fileContainer   = document.getElementById("fileContainer");
        const emptyTemplate   = document.getElementById("empty-template");

        try {
            const res = await fetch("https://pdu-dms.my.id/api/shared-with-me", {
                headers: {
                    "Authorization": "Bearer " + this.token,
                    "Accept": "application/json"
                }
            });

            if (!res.ok) {
                if (res.status === 401) return this.handleUnauthorized();
                throw new Error("Gagal memuat data");
            }

            const { data = [] } = await res.json();

            // Pisahkan folder & file berdasarkan is_folder
            const folders = data.filter(item => item.is_folder === true || item.is_folder === 1);
            const files   = data.filter(item => !item.is_folder && item.is_folder !== 1);

            // Normalisasi struktur agar sesuai dengan fungsi render lama kamu
            const normalizedFolders = folders.map(f => ({
                id: f.file_id || f.id,
                name: f.file_name || f.name,
                shared_by_name: f.shared_by?.name || f.shared_by_name || 'Someone'
            }));

            const normalizedFiles = files.map(f => ({
                id: f.file_id || f.id,
                name: f.file_name || f.name,
                mime: this.guessMime(f.file_name || f.name),
                labels: f.labels || [],
                url: f.file_path ? `https://pdu-dms.my.id/storage/${f.file_path}` : null
            }));

            // GUNAKAN FUNGSI ASLI KAMU 100% (tidak diubah sama sekali)
            this.renderFolders(normalizedFolders, folderContainer, emptyTemplate);
            this.renderFiles(normalizedFiles, fileContainer, emptyTemplate);

        } catch (err) {
            console.error(err);
            const msg = `<p class="text-danger text-center">Error: ${err.message}</p>`;
            folderContainer.innerHTML = msg;
            fileContainer.innerHTML   = msg;
        }
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
        folders.forEach(folder => {
            const col = document.createElement("div");
            col.className = "col-6 col-sm-4 col-md-3 col-lg-2 folder-item";
            col.innerHTML = this.createFolderHTML(folder);
            container.appendChild(col);
        });
    }

    createFolderHTML(folder) {
        return `
            <div class="position-relative">
                <div class="folder-card" style="cursor:pointer;">
                    <img src="/img/folder.svg" alt="Folder" class="img-fluid w-100 h-100 object-fit-contain" style="min-height:100px;">
                    <div class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <p class="fw-normal mt-2 mb-0 text-truncate" title="${folder.name}">${folder.name}</p>
                            <small class="fw-light">Shared by ${folder.shared_by_name || 'Someone'}</small>
                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="dropdown">
                                <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                    <i class="ph ph-dots-three-vertical fs-5 text-muted"></i>
                                </button>
                                <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="/shared-with-me/folder/${folder.id}">
                                        <i class="ph ph-arrow-up-right fs-5"></i> Open
                                    </a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2 folder-info-btn" href="#" data-folder='${JSON.stringify(folder)}'>
                                        <i class="ph ph-info fs-5"></i> Get Info
                                    </a></li>
                                </ul>
                            </div>
                        </div>
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

        const { icon } = this.getFileIcon(file.mime);
        // const openUrl = file.mime?.includes('pdf') ? `/files/${file.id}` : `/file-view/${file.id}`;
        const openUrl = file.is_folder
            ? `https://pdu-dms.my.id/api/my-files/${file.id}`
            : (file.mime?.includes('pdf') ? `/files/${file.id}` : `/file-view/${file.id}`);

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
                            <li><a class="dropdown-item d-flex align-items-center gap-2 info-btn" href="#" data-file='${JSON.stringify(file)}'>
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
        if (!mime) return { icon: "ph-file" };
        if (mime.includes("pdf")) return { icon: "ph-file-pdf" };
        if (mime.includes("doc") || mime.includes("document")) return { icon: "ph-file-doc" };
        if (mime.includes("xls") || mime.includes("sheet")) return { icon: "ph-file-xls" };
        return { icon: "ph-file" };
    }

    createLabelsHTML(labels) {
        if (!labels || labels.length === 0) return '<span class="badge bg-secondary rounded-2 px-2"><small>File</small></span>';
        return labels.map(l => `
            <span class="badge rounded-2 px-2 mb-1" style="background:#${l.color};color:${this.getTextColor(l.color)}">
                ${l.name}
            </span>
        `).join('');
    }

    getTextColor(bg) {
        const map = { "FDDCD9":"#CB564A", "EBE0D9":"#763E1A", "FDE9DD":"#C2825D", "EFEAFF":"#7762BB", "FCF9DE":"#BDB470", "E4F3FE":"#5F92B6", "FCE7ED":"#CA8499", "E6E5E3":"#989797", "EEFEF1":"#8ABB93", "F0EFED":"#729D9C" };
        return map[bg?.toUpperCase()] || "#000";
    }

    async renderPDFPreview(url, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        try {
            container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';
            const loadingTask = pdfjsLib.getDocument({ url, httpHeaders: { Authorization: "Bearer " + this.token } });
            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);
            const scale = Math.min(120 / page.getViewport({scale:1}).width, 1.5);
            const viewport = page.getViewport({scale});
            const canvas = document.createElement("canvas");
            canvas.width = viewport.width; canvas.height = viewport.height;
            canvas.style.width = "100%"; canvas.style.height = "100%";
            await page.render({canvasContext: canvas.getContext("2d"), viewport}).promise;
            container.innerHTML = ''; container.appendChild(canvas);
        } catch (e) {
            container.innerHTML = '<i class="ph ph-file-pdf fs-1 text-muted"></i>';
        }
    }

    attachGlobalListeners() {
        document.addEventListener("click", async e => {
            const btn = e.target.closest(".download-btn");
            if (!btn) return;
            e.preventDefault(); e.stopPropagation();
            const id = btn.dataset.id, name = btn.dataset.name;
            try {
                const res = await fetch(`https://pdu-dms.my.id/api/view-file/${id}`, {
                    headers: { Authorization: "Bearer " + this.token }
                });
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement("a"); a.href = url; a.download = name;
                a.click(); URL.revokeObjectURL(url);
            } catch (err) { alert("Download gagal"); }
        });

        document.addEventListener("click", e => {
            const btn = e.target.closest(".info-btn, .folder-info-btn");
            if (btn) alert("Info panel belum diimplementasikan di versi ringkas ini.\nTapi tampilan card-nya sudah sama persis!");
        });
    }

    handleUnauthorized() {
        alert("Session habis. Silakan login ulang.");
        window.location.href = "/signin";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new SharedWithMeManager();
});
</script>
@endsection
