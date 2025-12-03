// public/js/myspace.js

class MySpaceManager {
    constructor() {
        this.token = window.token || '';
        this.currentPath = window.currentPath || '';
        this.isLastOpenedPage = window.isLastOpenedPage || false;
        this.isRecommendedPage = window.isRecommendedPage || false;

        console.log('MySpaceManager initialized:', {
            isLastOpenedPage: this.isLastOpenedPage,
            isRecommendedPage: this.isRecommendedPage,
            currentPath: this.currentPath
        });

        this.init();
    }

    init() {
        console.log('MySpaceManager init called');
        this.loadFilesAndFolders();
        this.attachEventListeners();
    }

    attachSortListeners() {
        document.querySelectorAll(".sort-option").forEach(option => {
            option.addEventListener("click", (e) => {
                e.preventDefault();

                const sortType = option.dataset.sort;
                console.log("Sorting:", sortType);

                // Panggil ulang loader dgn sort
                this.loadFilesAndFolders(sortType);
            });
        });
    }


    async loadFilesAndFolders(sortType = null) {
        const folderContainer = document.getElementById("folderContainer");
        const fileContainer = document.getElementById("fileContainer");
        const emptyTemplate = document.getElementById("empty-template");

        if (!emptyTemplate) {
            console.error('Empty template not found!');
            return;
        }

        try {
            let url;
            let transformData = false;
            let folderData = null;

            // ✅ DETECT RECOMMENDED PAGE
            if (this.isRecommendedPage) {
                url = "https://pdu-dms.my.id/api/recommended-files";
                transformData = true;
                if (sortType) {
                    url += `?sort=${sortType}`;
                }
            }
            // DETECT LAST OPENED PAGE
            else if (this.isLastOpenedPage) {
                url = "https://pdu-dms.my.id/api/last-opened-files";
                transformData = true;
                if (sortType) {
                    url += `?sort=${sortType}`;
                }
            } else {
                const baseUrl = "https://pdu-dms.my.id/api/my-files";
                url = this.currentPath ? `${baseUrl}/${this.currentPath}` : baseUrl;
                transformData = false;
                if (sortType) {
                    url += `?sort=${sortType}`;
                }
            }

            console.log('Fetching from:', url);
            console.log('Page Type:', {
                isRecommendedPage: this.isRecommendedPage,
                isLastOpenedPage: this.isLastOpenedPage
            });

            const response = await fetch(url, {
                headers: {
                    "Authorization": "Bearer " + this.token,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    this.handleUnauthorized();
                    return;
                }
                throw new Error(`Gagal memuat data: ${response.status} ${response.statusText}`);
            }

            const data = await response.json();

            let folders = [];
            let files = [];

            if (transformData) {
                if (this.isRecommendedPage) {
                    // ✅ DATA DARI RECOMMENDED-FILES ENDPOINT (HANYA FILE)
                    files = data.recommended_files || [];
                    folders = []; // Recommended page hanya menampilkan files
                } else {
                    // DATA DARI LAST-OPENED-FILES ENDPOINT
                    folders = data.last_opened_folders || [];
                    files = data.last_opened_files || [];
                }
            } else {
                // DATA DARI MY-FILES ENDPOINT (ORIGINAL)
                folders = data.files?.filter(f => f.is_folder) || [];
                files = data.files?.filter(f => !f.is_folder) || [];
                folderData = data;
            }

            // ✅ UNTUK RECOMMENDED PAGE: Hanya render files, hide folder container
            if (this.isRecommendedPage) {
                if (folderContainer) {
                    folderContainer.style.display = 'none'; // Sembunyikan folder section
                }
                this.renderFiles(files, fileContainer, emptyTemplate.content.cloneNode(true));
            } else {
                // Untuk halaman lain: render normal
                if (folderContainer) {
                    await this.renderFolders(folders, folderContainer, emptyTemplate.content.cloneNode(true), folderData);
                }
                this.renderFiles(files, fileContainer, emptyTemplate.content.cloneNode(true));
            }

        } catch (err) {
            console.error('Error:', err);
            this.showError(folderContainer, fileContainer, err.message);
        }
    }

    async renderFolders(folders, container, emptyTemplate, folderData = null) {
    if (!container) return;

    container.innerHTML = '';

    if (folders.length === 0) {
        const empty = emptyTemplate.cloneNode(true);
        if (this.isLastOpenedPage) {
            empty.querySelector("i").className = "ph ph-folder-open";
            empty.querySelector("p").textContent = "No recently opened folders";
        } else {
            empty.querySelector("i").className = "ph ph-folder-open";
            empty.querySelector("p").textContent = "Create a folder to get organized";
        }
        container.appendChild(empty);
    } else {
        // ✅ GUNAKAN Promise.all UNTUK LOAD SEMUA FOLDER ITEM COUNT SECARA PARALEL
        const folderPromises = folders.map(async (folder) => {
            try {
                // Fetch data untuk setiap folder secara individual
                const response = await fetch(`https://pdu-dms.my.id/api/my-files/${folder.id}`, {
                    headers: {
                        "Authorization": "Bearer " + this.token,
                        "Accept": "application/json"
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const totalItems = data.files?.length || 0;
                    return { folder, totalItems };
                } else {
                    // Jika gagal, fallback ke size
                    return { folder, totalItems: 0 };
                }
            } catch (error) {
                console.error(`Error loading folder ${folder.id}:`, error);
                return { folder, totalItems: 0 };
            }
        });

        // Tunggu semua request selesai
        const foldersWithItems = await Promise.all(folderPromises);

        // Render semua folder dengan data yang sudah diload
        foldersWithItems.forEach(({ folder, totalItems }) => {
            const col = this.createFolderElement(folder, totalItems);
            container.appendChild(col);
        });
    }
}
    renderFiles(files, container, emptyTemplate) {
        if (!container) return;

        container.innerHTML = '';

        if (files.length === 0) {
            const empty = emptyTemplate.cloneNode(true);

            // ✅ CUSTOM MESSAGE UNTUK RECOMMENDED PAGE
            if (this.isRecommendedPage) {
                empty.querySelector("i").className = "ph ph-star";
                empty.querySelector("p").textContent = "No recommended files available";
            }
            // CUSTOM MESSAGE UNTUK LAST OPENED PAGE
            else if (this.isLastOpenedPage) {
                empty.querySelector("i").className = "ph ph-file";
                empty.querySelector("p").textContent = "No recently opened files";
            }
            // DEFAULT UNTUK MYSPACE
            else {
                empty.querySelector("i").className = "ph ph-file";
                empty.querySelector("p").textContent = "Upload your first file to begin";
            }

            container.appendChild(empty);
        } else {
            files.forEach(file => {
                const card = this.createFileElement(file);
                container.appendChild(card);
            });
        }
    }

    createFolderElement(folder, totalItems = 0) {
        const col = document.createElement("div");
        col.className = "col-6 col-sm-4 col-md-3 col-lg-2 folder-item";
        const folderPath = folder.id;

        // ✅ PERBAIKAN: GUNAKAN !== undefined ATAU null UNTUK totalItems
        const itemCountText = totalItems !== undefined && totalItems !== null ?
            `${totalItems} ${totalItems === 1 ? 'item' : 'items'}` :
            folder.size;

        col.innerHTML = `
            <div class="position-relative">
                <div class="folder-card" style="cursor: pointer;">
                    <img src="/img/folder.svg" alt="Folder" class="img-fluid w-100 h-100 object-fit-contain" style="min-height: 100px; min-width:120px">
                    <div class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <p class="fw-normal mt-2 mb-0 text-truncate" title="${folder.name}">${folder.name}</p>
                            <small class="fw-light">${itemCountText}</small>
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
                                            ${this.getFolderInfoPanelHTML(folder)}
                                        </div>
                                    </li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 advanced-share-btn"
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#advancedShareModal"
                                            data-item-id="${folder.id}"
                                            data-item-type="folder"
                                            data-folder-id="${folder.id}"
                                            data-folder-name="${folder.name}"
                                            data-folder-items="${folder.size}">
                                             <i class="ph ph-share-network fs-5"></i> Share
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#"
                                        class="dropdown-item d-flex align-items-center gap-2 duplicate-btn"
                                        data-id="${folder.id}"
                                        data-name="${folder.name}">
                                        <i class="ph ph-copy fs-5"></i> Duplicate
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
                                        <a class="dropdown-item d-flex align-items-center gap-2 folder-rename-btn"
                                        href="#"
                                        data-id="${folder.id}"
                                        data-name="${folder.name}">
                                            <i class="ph ph-pencil-simple fs-5"></i> Rename
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

        return col;
    }

createFileElement(file) {
    const card = document.createElement("div");
    card.className = "card rounded-4 border-dark-subtle border-1 me-3 file-card";
    card.style.width = "160px";
    card.style.height = "180px";
    card.style.backgroundColor = "#F2F2F0";
    card.style.cursor = "pointer";

    const fileInfo = this.getFileIconAndType(file.mime);
    const openUrl = file.mime && file.mime.includes('pdf') ?
        `/files/${file.id}` :
        `/file-view/${file.id}`;

    const labelsHTML = this.createLabelsHTML(file.labels || []);

    card.innerHTML = `
        <!-- ✅ PREVIEW CONTAINER DENGAN FIXED HEIGHT -->
        <div class="mt-3 mx-2 preview-container" style="height: 100px; display: flex; align-items: center; justify-content: center;">
            <div id="preview-${file.id}" class="d-flex justify-content-center align-items-center w-100 h-100">
                <i class="ph ${fileInfo.icon} fs-1 text-muted"></i>
            </div>
        </div>

        <div class="card-body p-2 d-flex flex-column" style="height: calc(220px - 100px - 1rem);">
            <!-- ✅ FILE NAME -->
            <div class="d-flex align-items-center mb-1">
                <i class="ph ${fileInfo.icon} me-2 text-dark"></i>
                <span class="fw-semibold text-truncate small" title="${file.name}">${file.name}</span>
            </div>

            <!-- ✅ LABELS & ACTIONS SECTION - SEJAJAR -->
            <div class="d-flex align-items-start justify-content-between mb-1 flex-grow-1" style="min-height: 30px;">
                <!-- ✅ LABELS SECTION -->
                <div class="labels-section flex-grow-1 me-2" style="overflow: hidden;">
                    ${labelsHTML}
                </div>

                <!-- ✅ DROPDOWN ACTIONS -->
                <div class="dropdown flex-shrink-0">
                    <button class="btn btn-link text-dark p-0"
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


                        <li class="dropdown-submenu position-relative">
                            <a class="dropdown-item d-flex align-items-center gap-2 info-btn"
                            href="#"
                            data-file-id="${file.id}">
                                <i class="ph ph-info fs-5"></i> Get Info
                            </a>
                            <div class="file-info-panel" style="display: none; position: absolute; left: 100%; top: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border: 1px solid #e9ecef;">
                                ${this.getFileInfoPanelHTML(file, fileInfo)}
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 edit-file-btn" href="#"
                            data-id="${file.id}" data-name="${file.name}" data-labels='${JSON.stringify(file.labels || [])}'>
                                <i class="ph ph-pencil-simple fs-5"></i> Edit File
                            </a>
                        </li>
                        <li>
                            <a href="#"
                            class="dropdown-item d-flex align-items-center gap-2 duplicate-btn"
                            data-id="${file.id}"
                            data-name="${file.name}">
                            <i class="ph ph-copy fs-5"></i> Duplicate
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 advanced-share-btn"
                            href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#advancedShareModal"
                            data-item-id="${file.id}"
                            data-item-type="file"
                            data-file-id="${file.id}"
                            data-file-name="${file.name}"
                            data-file-items="${file.size}"
                            data-mime="${file.mime || ''}">
                                <i class="ph ph-share-network fs-5"></i> Share
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

    // Add click event for card
    card.addEventListener('click', (e) => {
        if (e.target.closest('.dropdown') || e.target.closest('.dropdown-menu')) {
            return;
        }
        window.open(openUrl, '_blank');
    });

    // Render PDF preview if applicable
    if (file.mime && file.mime.includes("pdf") && file.url) {
        setTimeout(() => {
            this.renderPDFPreview(file.url, `preview-${file.id}`);
        }, 100);
    }

    return card;
}

/**
 * Create HTML for labels - Tampilkan SEMUA labels dengan color mapping
 */
createLabelsHTML(labels) {
    if (!labels || labels.length === 0) {
        return '<span class="badge bg-secondary rounded-2 px-2"><small>File</small></span>';
    }

    const allLabelsHTML = labels.map(label => {
        // ✅ PAKAI COLOR MAPPING YANG SUDAH ADA
        const textColor = this.getMappedTextColor(label.color);
        return `
            <span class="badge rounded-2 px-2 mb-1 flex-shrink-0"
                  style="background-color: #${label.color}; color: ${textColor}; border: 1px solid #ddd; font-size: 0.7rem; line-height: 1.2; font-family: 'Rubik', sans-serif; font-weight: 400;"
                  title="${label.name}">
                ${label.name}
            </span>
        `;
    }).join('');

    return `
        <div class="labels-wrap-container" style="display: flex; flex-wrap: wrap; gap: 2px; max-height: 40px; overflow: hidden;">
            ${allLabelsHTML}
        </div>
    `;
}

/**
 * Get text color dari mapping yang sudah ada
 */
getMappedTextColor(backgroundColor) {
    // 🎨 Map background → text color (sama seperti di sidebar)
    const colorMap = {
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

    // Normalize color code (hilangkan # jika ada, uppercase)
    const normalizedColor = backgroundColor.replace('#', '').toUpperCase();

    // Return mapped color atau fallback ke hitam
    return colorMap[normalizedColor] || '#000000';
}


    getFileIconAndType(mime) {
        if (!mime) return { icon: "ph-file", type: "File" };

        if (mime.includes("pdf")) {
            return { icon: "ph-file-pdf", type: "PDF" };
        } else if (mime.includes("docs") || mime.includes("document")) {
            return { icon: "ph-file-doc", type: "DOC" };
        } else if (mime.includes("xlsx") || mime.includes("spreadsheet")) {
            return { icon: "ph-file-xls", type: "XLSX" };
        } else if (mime.includes("ppt")) {
            return { icon: "ph-file-image", type: "Image" };
        }

        return { icon: "ph-file", type: "File" };
    }

    getFolderInfoPanelHTML(folder) {
        return `
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
        `;
    }

    getFileInfoPanelHTML(file, fileInfo) {
        return `
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">File Information</h6>
                    <button type="button" class="btn-close close-info-panel" style="font-size: 0.7rem;"></button>
                </div>

                <div class="text-center mb-3">
                    <div class="file-preview-icon mx-auto mb-2">
                        <i class="ph ${fileInfo.icon} fs-1 text-muted"></i>
                    </div>
                    <h6 class="fw-semibold mb-1 text-truncate">${file.name}</h6>
                    <small class="text-muted">${file.type} • ${file.size}</small>
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
                                <p class="mb-0 fw-medium">${file.type}</p>
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
        `;
    }

    // Tambahkan ini sebagai method di dalam class MySpaceManager
    async renderFileThumbnail(fileId, mimeType = '') {
        const wrapper = document.getElementById('file-thumbnail-wrapper');
        const loading = document.getElementById('file-thumbnail-loading');
        if (!wrapper || !loading) return;

        wrapper.innerHTML = '';
        loading.classList.remove('d-none');

        let url = null;

        try {
            const response = await fetch(`https://pdu-dms.my.id/api/view-file/${fileId}`, {
                headers: {
                    'Authorization': 'Bearer ' + this.token
                }
            });

            if (!response.ok) throw new Error('Failed to load file');

            const blob = await response.blob();
            url = URL.createObjectURL(blob);
            const type = blob.type || mimeType;

            let element;

            // === GAMBAR ===
            if (type.startsWith('image/') && !type.includes('svg')) {
                element = document.createElement('img');
                element.src = url;
                element.className = 'w-100 h-100';
                element.style.objectFit = 'cover';
                element.style.borderRadius = '8px';
            }

            // === PDF ===
            else if (type === 'application/pdf') {
                const loadingTask = pdfjsLib.getDocument({ url });
                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1);

                // Auto-scale agar pas di 120px tinggi
                const viewport = page.getViewport({ scale: 1 });
                let scale = 120 / viewport.height;
                if (scale > 2) scale = 2; // batas atas biar gak blur

                const finalViewport = page.getViewport({ scale });

                const canvas = document.createElement('canvas');
                canvas.width = finalViewport.width;
                canvas.height = finalViewport.height;
                canvas.style.width = '100%';
                canvas.style.height = '100%';
                canvas.style.objectFit = 'contain';
                canvas.style.background = 'white';
                canvas.style.borderRadius = '8px';

                const context = canvas.getContext('2d');
                await page.render({ canvasContext: context, viewport: finalViewport }).promise;

                element = canvas;
            }

            // === VIDEO ===
            else if (type.startsWith('video/')) {
                element = document.createElement('video');
                element.src = url;
                element.muted = true;
                element.preload = 'metadata';
                element.playsInline = true;
                element.className = 'w-100 h-100';
                element.style.objectFit = 'cover';
                element.style.borderRadius = '8px';

                // Ambil poster otomatis dari frame pertama
                element.addEventListener('loadeddata', () => {
                    element.currentTime = 0.1;
                });
                element.addEventListener('seeked', () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = element.videoWidth;
                    canvas.height = element.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(element, 0, 0, canvas.width, canvas.height);
                    element.poster = canvas.toDataURL();
                });
            }

            // === TIDAK DIDUKUNG ===
            else {
                throw new Error('No visual preview');
            }

            wrapper.appendChild(element);

        } catch (error) {
            console.warn('Preview gagal:', error.message);

            // Fallback icon cerdas (sama persis seperti kode lama)
            const iconMap = {
                'pdf': 'ph-file-pdf',
                'word': 'ph-file-doc',
                'document': 'ph-file-doc',
                'excel': 'ph-file-xls',
                'sheet': 'ph-file-xls',
                'powerpoint': 'ph-file-ppt',
                'ppt': 'ph-file-ppt',
                'video': 'ph-file-video',
                'audio': 'ph-file-audio',
                'zip': 'ph-file-zip',
                'default': 'ph-file'
            };

            let icon = iconMap.default;
            const lowerMime = (mimeType || '').toLowerCase();

            if (lowerMime.includes('pdf')) icon = iconMap.pdf;
            else if (lowerMime.includes('word') || lowerMime.includes('document')) icon = iconMap.word;
            else if (lowerMime.includes('excel') || lowerMime.includes('sheet')) icon = iconMap.excel;
            else if (lowerMime.includes('powerpoint') || lowerMime.includes('ppt')) icon = iconMap.powerpoint;
            else if (lowerMime.includes('video')) icon = iconMap.video;
            else if (lowerMime.includes('audio')) icon = iconMap.audio;
            else if (lowerMime.includes('zip') || lowerMime.includes('rar')) icon = iconMap.zip;

            wrapper.innerHTML = `<i class="ph ${icon} fs-1 text-muted"></i>`;

        } finally {
            loading.classList.add('d-none');
            // Bersihkan memory setelah 10 detik
            if (url) {
                setTimeout(() => URL.revokeObjectURL(url), 10000);
            }
        }
    }
    openAdvancedShareModal(data) {
        const folderPreview = document.getElementById('folder-preview');
        const filePreview = document.getElementById('file-preview');
        const wrapper = document.getElementById('file-thumbnail-wrapper');

        // Reset
        folderPreview.style.display = 'none';
        filePreview.style.display = 'none';
        wrapper.innerHTML = '';

        if (data.type === 'folder') {
            folderPreview.style.display = 'block';
            document.getElementById('preview-title').textContent = data.name;
            document.getElementById('preview-subtitle').textContent = data.items ? `${data.items} items` : 'Empty folder';
        } else {
            filePreview.style.display = 'block';
            document.getElementById('file-name-display').textContent = data.name;
            document.getElementById('file-size-display').textContent = data.size || '—';

            let badgeText = 'File';
            let iconClass = 'ph-file';

            if (data.mime) {
                if (data.mime.includes('pdf')) { badgeText = 'PDF'; iconClass = 'ph-file-pdf'; }
                else if (data.mime.includes('image/')) { badgeText = 'Image'; iconClass = 'ph-file-image'; }
                else if (data.mime.includes('word')) { badgeText = 'DOC'; iconClass = 'ph-file-doc'; }
                else if (data.mime.includes('excel') || data.mime.includes('sheet')) { badgeText = 'XLS'; iconClass = 'ph-file-xls'; }
            }

            document.querySelector('#file-badge small').textContent = badgeText;
            document.getElementById('file-small-icon').className = `${iconClass} me-2 text-dark`;

            // Render thumbnail
            if (data.mime && (data.mime.includes('pdf') || data.mime.includes('image/'))) {
                this.renderFileThumbnail(data.id, data.mime);
            } else {
                wrapper.innerHTML = `<i class="ph ${iconClass} fs-1 text-muted"></i>`;
            }
        }

        const modal = new bootstrap.Modal(document.getElementById('advancedShareModal'));
        modal.show();
    }

    async duplicateFile(fileId, fileName, buttonElement) {
        if (!this.token) {
            alert("Token tidak ditemukan. Silakan login ulang.");
            return;
        }

        try {
            // Show loading state on button
            const originalText = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="ph ph-spinner ph-spin fs-5"></i> Duplicating...';
            buttonElement.disabled = true;

            const response = await fetch("https://pdu-dms.my.id/api/duplicate-file", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + this.token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    file_id: parseInt(fileId)
                })
            });

            const result = await response.json();

            if (response.ok) {
                // Success - reload files to show the duplicated file
                this.loadFilesAndFolders();

                // Show success message
                this.showDuplicateSuccessMessage(fileName);

            } else {
                let errorMessage = "Gagal menduplikat file";
                if (result.message) {
                    errorMessage += ": " + result.message;
                } else if (response.status === 404) {
                    errorMessage = "File tidak ditemukan";
                } else if (response.status === 403) {
                    errorMessage = "Anda tidak memiliki izin untuk menduplikat file ini";
                } else if (response.status === 422) {
                    errorMessage = "Data tidak valid";
                }
                throw new Error(errorMessage);
            }

        } catch (err) {
            console.error('Duplicate file error:', err);
            alert("Gagal menduplikat file: " + err.message);
        } finally {
            // Reset button state
            if (buttonElement) {
                buttonElement.innerHTML = originalText;
                buttonElement.disabled = false;
            }
        }
    }

    showDuplicateSuccessMessage(fileName) {
        // Create a custom success notification
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        successDiv.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;
        successDiv.innerHTML = `
            <i class="ph ph-check-circle me-2"></i>
            <strong>Success!</strong> File "${fileName}" berhasil diduplikat.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(successDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (successDiv.parentNode) {
                successDiv.remove();
            }
        }, 5000);
    }
    async renderPDFPreview(pdfUrl, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    try {
        container.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div>';

        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            httpHeaders: {
                'Authorization': 'Bearer ' + this.token
            }
        });

        const pdf = await loadingTask.promise;
        const page = await pdf.getPage(1);

        // ✅ FIXED CONTAINER DIMENSIONS
        const containerWidth = 120; // Lebar maksimal container
        const containerHeight = 80; // Tinggi maksimal container

        const originalViewport = page.getViewport({ scale: 1 });
        const scale = Math.min(
            containerWidth / originalViewport.width,
            containerHeight / originalViewport.height
        );

        const viewport = page.getViewport({ scale });

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        // ✅ STYLE KONSISTEN DENGAN FIXED CONTAINER
        canvas.style.width = `${viewport.width}px`;
        canvas.style.height = `${viewport.height}px`;
        canvas.style.maxWidth = '100%';
        canvas.style.objectFit = 'contain';
        canvas.style.borderRadius = '4px';
        canvas.style.backgroundColor = '#f8f9fa';
        canvas.style.display = 'block';
        canvas.style.margin = '0 auto';

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

    attachEventListeners() {
        this.attachInfoPanelListeners();
        this.attachFileOperationsListeners();
        this.attachFolderOperationsListeners();
        this.attachRenameFolderListeners();
        this.attachSortListeners();
    }

    attachInfoPanelListeners() {
        // Event listener untuk info button (file)
        document.addEventListener("click", (e) => {
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
                this.loadFileLocation(fileId, infoPanel);

                // Adjust position jika perlu
                const rect = infoPanel.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    infoPanel.style.left = 'auto';
                    infoPanel.style.right = '100%';
                }
            }
        });

        // Event listener untuk folder info button
        document.addEventListener("click", (e) => {
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
                this.loadFolderLocation(folderId, folderInfoPanel);

                // Adjust position jika perlu
                const rect = folderInfoPanel.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    folderInfoPanel.style.left = 'auto';
                    folderInfoPanel.style.right = '100%';
                }
            }
        });

        // Event listener untuk close info panel
        document.addEventListener("click", (e) => {
            const closeBtn = e.target.closest(".close-info-panel");
            if (closeBtn) {
                e.preventDefault();
                e.stopPropagation();
                const infoPanel = closeBtn.closest('.file-info-panel');
                infoPanel.style.display = 'none';
            }
        });

        // Event listener untuk close folder info panel
        document.addEventListener("click", (e) => {
            const closeBtn = e.target.closest(".folder-close-info-panel");
            if (closeBtn) {
                e.preventDefault();
                e.stopPropagation();
                const folderInfoPanel = closeBtn.closest('.folder-info-panel');
                folderInfoPanel.style.display = 'none';
            }
        });

        // Sembunyikan info panel ketika klik di luar
        document.addEventListener("click", (e) => {
            if (!e.target.closest('.dropdown-submenu') && !e.target.closest('.file-info-panel') && !e.target.closest('.folder-info-panel')) {
                document.querySelectorAll('.file-info-panel').forEach(panel => {
                    panel.style.display = 'none';
                });
                document.querySelectorAll('.folder-info-panel').forEach(panel => {
                    panel.style.display = 'none';
                });
            }
        });

        // Ganti yang lama dengan ini
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.advanced-share-btn');
    if (!btn) return;
    e.preventDefault();

    const isFolder = btn.hasAttribute('data-folder-id');
    const isFile = btn.hasAttribute('data-file-id');

    if (isFolder) {
        this.openAdvancedShareModal({
            type: 'folder',
            id: btn.dataset.folderId,
            name: btn.dataset.folderName,
            items: btn.dataset.folderItems
        });
    } else if (isFile) {
        this.openAdvancedShareModal({
            type: 'file',
            id: btn.dataset.fileId,
            name: btn.dataset.fileName,
            size: btn.dataset.fileItems,
            mime: btn.dataset.mime || ''
        });
    }
});
    }

    attachFileOperationsListeners() {
    // Event listener untuk duplicate file
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".duplicate-btn");
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const fileId = btn.getAttribute("data-id");
            const fileName = btn.getAttribute("data-name");

            await this.duplicateFile(fileId, fileName, btn);
        });
        // Event listener untuk delete file
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".delete-btn");
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const fileId = btn.getAttribute("data-id");

            if (!confirm("Yakin mau menghapus file ini?")) return;

            try {
                const response = await fetch(`https://pdu-dms.my.id/api/delete-file/${fileId}`, {
                    method: "DELETE",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer " + this.token,
                        "Content-Type": "application/json"
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    const fileCard = btn.closest(".file-card");
                    if (fileCard) {
                        fileCard.remove();

                        const fileContainer = document.getElementById("fileContainer");
                        const remainingFiles = fileContainer.querySelectorAll('.file-card');
                        if (remainingFiles.length === 0) {
                            const emptyTemplate = document.getElementById("empty-template").content.cloneNode(true);
                            emptyTemplate.querySelector("i").className = "ph ph-file";
                            emptyTemplate.querySelector("p").textContent = "Upload your first file to begin";
                            fileContainer.appendChild(emptyTemplate);
                        }
                    }
                    // alert("File berhasil dihapus");
                } else {
                    let errorMessage = "Gagal menghapus file";
                    if (result.message) {
                        errorMessage += ": " + result.message;
                    } else if (response.status === 404) {
                        errorMessage = "File tidak ditemukan atau sudah dihapus";
                    } else if (response.status === 403) {
                        errorMessage = "Anda tidak memiliki izin untuk menghapus file ini";
                    }
                    alert(errorMessage);
                }

            } catch (err) {
                console.error('Delete file error:', err);
                alert("Gagal menghapus file: " + err.message);
            }
        });

        // Event listener untuk download file
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".download-btn");
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const fileId = btn.getAttribute("data-id");
            const fileName = btn.getAttribute("data-name");

            if (!this.token) {
                alert("Token tidak ditemukan. Silakan login ulang.");
                return;
            }

            try {
                const response = await fetch(`https://pdu-dms.my.id/api/view-file/${fileId}`, {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + this.token,
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
    }

    attachFolderOperationsListeners() {
        // Event listener untuk delete folder
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".folder-delete-btn");
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const folderId = btn.getAttribute("data-id");
            const folderName = btn.getAttribute("data-name");

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
                        "Authorization": "Bearer " + this.token,
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
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".folder-download-btn");
            if (!btn) return;

            e.preventDefault();

            const folderId = btn.getAttribute("data-id");
            const folderName = btn.getAttribute("data-name");

            if (!this.token) {
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
                        "Authorization": "Bearer " + this.token,
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
                        "Authorization": "Bearer " + this.token
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
        document.addEventListener("click", (e) => {
            const folderCard = e.target.closest('.folder-card');
            if (folderCard && !e.target.closest('.dropdown') && !e.target.closest('.dropdown-menu')) {
                const folderItem = folderCard.closest('.folder-item');
                const openBtn = folderItem.querySelector('.folder-open-btn');
                if (openBtn) {
                    window.location.href = openBtn.getAttribute('href');
                }
            }
        });
    }

    attachRenameFolderListeners() {
        // Event listener untuk rename folder button
        document.addEventListener("click", (e) => {
            const renameBtn = e.target.closest(".folder-rename-btn");
            if (renameBtn) {
                e.preventDefault();
                e.stopPropagation();

                const folderId = renameBtn.getAttribute("data-id");
                const currentName = renameBtn.getAttribute("data-name");

                this.openRenameFolderModal(folderId, currentName);
            }
        });

        // Event listener untuk confirm rename
        const confirmBtn = document.getElementById('confirmRenameFolder');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', async () => {
                await this.renameFolder();
            });
        }

        // Event listener untuk Enter key pada input field
        const nameInput = document.getElementById('newFolderName');
        if (nameInput) {
            nameInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.renameFolder();
                }
            });
        }

        // Reset form ketika modal ditutup
        const modal = document.getElementById('renameFolderModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                document.getElementById('renameFolderForm').reset();
            });
        }
    }

    openRenameFolderModal(folderId, currentName) {
        document.getElementById('renameFolderId').value = folderId;
        document.getElementById('newFolderName').value = currentName;

        const modal = new bootstrap.Modal(document.getElementById('renameFolderModal'));
        modal.show();

        // Focus pada input field
        setTimeout(() => {
            document.getElementById('newFolderName').focus();
            document.getElementById('newFolderName').select();
        }, 500);
    }

    async renameFolder() {
        const folderId = document.getElementById('renameFolderId').value;
        const newName = document.getElementById('newFolderName').value.trim();

        if (!newName) {
            alert('Folder name cannot be empty');
            return;
        }

        // Validasi nama folder
        if (!this.isValidFolderName(newName)) {
            alert('Folder name contains invalid characters. Please use only letters, numbers, spaces, hyphens, and underscores.');
            return;
        }

        try {
            const renameBtn = document.getElementById('confirmRenameFolder');
            const originalText = renameBtn.innerHTML;

            // Show loading state
            renameBtn.innerHTML = '<i class="ph ph-spinner ph-spin fs-5"></i> Renaming...';
            renameBtn.disabled = true;

            const response = await fetch(`https://pdu-dms.my.id/api/update-file/${folderId}`, {
                method: "PATCH",
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + this.token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name: newName
                })
            });

            const result = await response.json();

            if (response.ok) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('renameFolderModal'));
                modal.hide();

                // Update UI
                this.updateFolderNameInUI(folderId, newName);
                alert('Folder renamed successfully!');

            } else {
                throw new Error(result.message || 'Failed to rename folder');
            }

        } catch (error) {
            console.error('Error renaming folder:', error);
            alert('Failed to rename folder: ' + error.message);
        } finally {
            // Reset button state
            const renameBtn = document.getElementById('confirmRenameFolder');
            renameBtn.innerHTML = 'Rename';
            renameBtn.disabled = false;
        }
    }

    updateFolderNameInUI(folderId, newName) {
        const folderElement = document.querySelector(`.folder-rename-btn[data-id="${folderId}"]`)?.closest('.folder-item');
        if (!folderElement) return;

        // ileolder name in the card
        const folderNameElement = folderElement.querySelector('.fw-normal');
        if (folderNameElement) {
            folderNameElement.textContent = newName;
            folderNameElement.setAttribute('title', newName);
        }

        // Update data attributes in rename button
        const renameBtn = folderElement.querySelector('.folder-rename-btn');
        if (renameBtn) {
            renameBtn.setAttribute('data-name', newName);
        }

        // Update data attributes in info button
        const infoBtn = folderElement.querySelector('.folder-info-btn');
        if (infoBtn) {
            infoBtn.setAttribute('data-folder-name', newName);
        }

        // Update data attributes in delete button
        const deleteBtn = folderElement.querySelector('.folder-delete-btn');
        if (deleteBtn) {
            deleteBtn.setAttribute('data-name', newName);
        }

        // Update data attributes in download button
        const downloadBtn = folderElement.querySelector('.folder-download-btn');
        if (downloadBtn) {
            downloadBtn.setAttribute('data-name', newName);
        }
    }

    isValidFolderName(name) {
        const regex = /^[a-zA-Z0-9\s\-_()]+$/;
        return regex.test(name);
    }

    async loadFileLocation(fileId, infoPanel) {
        const locationElement = infoPanel.querySelector(`#location-${fileId}`);
        if (!locationElement) return;

        try {
            const fileResponse = await fetch(`https://pdu-dms.my.id/api/my-files`, {
                headers: {
                    "Authorization": "Bearer " + this.token
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

            const breadcrumb = await this.buildFileBreadcrumb(currentFile.parent_id);
            locationElement.textContent = breadcrumb;

        } catch (error) {
            console.error('Error loading file location:', error);
            locationElement.textContent = "MySpace";
        }
    }

    async loadFolderLocation(folderId, folderInfoPanel) {
        const locationElement = folderInfoPanel.querySelector(`#folder-location-${folderId}`);
        if (!locationElement) return;

        try {
            const breadcrumb = await this.buildFolderBreadcrumb(folderId);
            locationElement.textContent = breadcrumb;

        } catch (error) {
            console.error('Error loading folder location:', error);
            locationElement.textContent = "MySpace";
        }
    }

    async buildFileBreadcrumb(parentId) {
        if (!parentId) return "MySpace";

        try {
            const breadcrumb = ["MySpace"];
            let currentId = parentId;

            for (let i = 0; i < 10; i++) {
                const response = await fetch(`https://pdu-dms.my.id/api/folders/${currentId}`, {
                    headers: {
                        "Authorization": "Bearer " + this.token
                    }
                });

                if (!response.ok) break;

                const folderData = await response.json();
                const folder = folderData.folder || folderData;

                if (folder && folder.name) {
                    breadcrumb.unshift(folder.name);
                    currentId = folder.parent_id;

                    if (!currentId) break;
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

    async buildFolderBreadcrumb(folderId) {
        if (!folderId) return "MySpace";

        try {
            const breadcrumb = [];
            let currentId = folderId;

            for (let i = 0; i < 10; i++) {
                const response = await fetch(`https://pdu-dms.my.id/api/folders/${currentId}`, {
                    headers: {
                        "Authorization": "Bearer " + this.token
                    }
                });

                if (!response.ok) break;

                const folderData = await response.json();
                const folder = folderData.folder || folderData;

                if (folder && folder.name) {
                    breadcrumb.unshift(folder.name);
                    currentId = folder.parent_id;

                    if (!currentId) {
                        breadcrumb.unshift("MySpace");
                        break;
                    }
                } else {
                    break;
                }
            }

            if (breadcrumb.length === 0) {
                breadcrumb.push("MySpace");
            }

            return breadcrumb.join(' / ');
        } catch (error) {
            console.error('Error building folder breadcrumb:', error);
            return "MySpace";
        }
    }

    handleUnauthorized() {
        alert('Session expired. Please login again.');
        window.location.href = "/signin";
    }

    showError(folderContainer, fileContainer, message) {
        if (folderContainer) {
            folderContainer.innerHTML = `<p class="text-danger">Gagal memuat data: ${message}</p>`;
        }
        if (fileContainer) {
            fileContainer.innerHTML = `<p class="text-danger">Gagal memuat data: ${message}</p>`;
        }
    }
}

// Inisialisasi ketika DOM siap
document.addEventListener("DOMContentLoaded", function() {
    window.mySpaceManager = new MySpaceManager();
});

class ShareManager {
    constructor() {
        this.selectedUsers = [];
        this.token = window.token || '';
        this.itemId = null;      // ID file/folder yang sedang dishare
        this.itemType = null;    // 'file' atau 'folder'
        this.debounceTimer = null;

        this.init();
    }

    init() {
        const modalEl = document.getElementById('advancedShareModal');
        if (!modalEl) return;

        // Ambil data dari trigger button saat modal dibuka
        modalEl.addEventListener('show.bs.modal', (e) => {
            const button = e.relatedTarget;
            this.itemId = button.getAttribute('data-item-id');
            this.itemType = button.getAttribute('data-item-type') || 'file';
            this.selectedUsers = []; // reset
            document.getElementById('selected-emails-container').innerHTML = '';
        });

        modalEl.addEventListener('shown.bs.modal', () => {
            this.setupInputEvents();
        });

        // Tombol Done
        document.getElementById('share-done-btn')?.addEventListener('click', () => {
            this.shareItem();
        });
    }

    setupInputEvents() {
        const wrapper = document.getElementById('email-input-wrapper');
        const input = document.getElementById('add-email-input');
        const suggestions = document.getElementById('email-suggestions');

        if (!wrapper || !input) return;

        wrapper.addEventListener('click', () => input.focus());

        input.addEventListener('input', () => {
            clearTimeout(this.debounceTimer);
            const q = input.value.trim();
            if (q.length < 2) {
                suggestions.style.display = 'none';
                return;
            }
            this.debounceTimer = setTimeout(() => this.searchUsers(q), 300);
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const first = suggestions.querySelector('.suggestion-item');
                if (first) first.click();
            }
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });
    }

    async searchUsers(query) {
        const list = document.getElementById('suggestions-list');
        const suggestions = document.getElementById('email-suggestions');

        try {
            list.innerHTML = '<div class="text-center py-3"><small class="text-muted">Searching...</small></div>';
            suggestions.style.display = 'block';

            const res = await fetch(`https://pdu-dms.my.id/api/search-users?q=${encodeURIComponent(query)}`, {
                headers: { 'Authorization': 'Bearer ' + this.token }
            });

            if (!res.ok) throw new Error('Failed');

            const users = await res.json();
            list.innerHTML = '';

            users.forEach(user => {
                if (this.selectedUsers.find(u => u.id === user.id)) return;

                const item = document.createElement('div');
                item.className = 'suggestion-item d-flex align-items-center gap-3 p-3 rounded-3 hover-bg-light cursor-pointer';
                item.innerHTML = `
                    <img src="${user.photo_profile_path
                    ? 'https://pdu-dms.my.id/storage/profile_photos/' + user.photo_profile_path
                    : '/images/profile-pict.jpg'}"
                    class="rounded-circle object-fit-cover flex-shrink-0" width="36" height="36"
                    onerror="this.src='/images/profile-pict.jpg'">

                    <div>
                        <div class="fw-semibold small">${user.fullname || 'No Name'}</div>
                        <div class="text-muted small">${user.email}</div>
                    </div>
                `;

                item.onclick = () => {
                    this.addUserPill(user);
                    document.getElementById('add-email-input').value = '';
                    suggestions.style.display = 'none';
                };

                list.appendChild(item);
            });

            if (users.length === 0) {
                list.innerHTML = '<div class="text-center py-3 text-muted small">No users found</div>';
            }

        } catch (err) {
            list.innerHTML = '<div class="text-center py-3 text-danger small">Error loading users</div>';
        }
    }

    addUserPill(user) {
        if (this.selectedUsers.find(u => u.id === user.id)) return;

        this.selectedUsers.push(user);

        const pill = document.createElement('div');
        pill.className = 'd-inline-flex align-items-center bg-white border rounded-pill px-3 py-1 gap-2 shadow-sm';
        pill.innerHTML = `
            <img src="${user.photo_profile_path
            ? 'https://pdu-dms.my.id/storage/profile_photos/' + user.photo_profile_path
            : '/images/profile-pict.jpg'}"
             class="rounded-circle object-fit-cover flex-shrink-0" width="22" height="22"
             onerror="this.src='/images/profile-pict.jpg'">
            <span class="small fw-medium text-dark">${user.email}</span>
            <button type="button" class="btn-close btn-close-sm" style="font-size: 0.55rem;"></button>
        `;

        pill.querySelector('.btn-close').onclick = (e) => {
            e.stopPropagation();
            this.selectedUsers = this.selectedUsers.filter(u => u.id !== user.id);
            pill.remove();
        };

        document.getElementById('selected-emails-container').appendChild(pill);
    }

    // INI YANG PALING PENTING: KIRIM SHARE KE API
    async shareItem() {
    if (!this.itemId || this.selectedUsers.length === 0) {
        alert('Pilih item dan minimal satu penerima!');
        return;
    }

    const btn = document.getElementById('share-done-btn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sharing...';

    try {
        // PAKAI FORMAT EXACTLY SAMA DENGAN POSTMAN
        const payload = {
            emails: this.selectedUsers.map(u => u.email),  // KIRIM EMAIL, BUKAN ID!
            permission_id: 4  // viewer
            // TIDAK USAH KIRIM file_id / folder_id → sudah ada di URL: /share-file/{id}
        };

        const response = await fetch(`https://pdu-dms.my.id/api/share-file/${this.itemId}`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + this.token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal membagikan');
        }

        alert(`Berhasil dibagikan ke ${this.selectedUsers.length} orang!`);
        bootstrap.Modal.getInstance(document.getElementById('advancedShareModal')).hide();

    } catch (err) {
        console.error('Share error:', err);
        alert('Gagal: ' + (err.message || 'Server error'));
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}
}

// Inisialisasi global
document.addEventListener('DOMContentLoaded', () => {
    window.shareManager = new ShareManager();
});
