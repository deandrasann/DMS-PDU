<div id="sidebar"
    class="d-flex vh-100 flex-column flex-shrink-0 p-3 bg-light shadow mb-2 rounded-4 position-fixed sidebar-full"
    style="width: 280px; transition: width 0.3s;">
    <div class="d-flex align-items-center justify-content-between mb-3 position-relative">
        <div class="d-flex align-items-center">
            <img src="{{ asset('storage/images/logo v1.png') }}" class="d-flex align-items-center ms-2 p-1"
                alt="Logo Kecil" id="sidebarLogoSmall" style="width:36px;">

            <!-- Logo besar (muncul saat expanded) -->
            <img src="{{ asset('storage/images/logo v2.png') }}" alt="Logo Besar" id="sidebarLogoBig"
                style="width:120px;">
        </div>
        <!-- Tombol toggle -->
        <button id="toggleBtn" class="btn btn-light bg-white ms-2">
            <i class="ph-bold ph-sidebar-simple"></i>
        </button>
    </div>
    <!-- Tombol Upload dengan Dropdown -->
    <div class="dropdown mt-4 mb-2">
        <a href="#" class="shadow p-2 text-decoration-none text-dark border rounded-4 d-flex align-items-center"
            id="uploadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ph ph-bold ph-plus fs-3 me-2 my-2"></i>
            <span class="sidebar-text fw-normal">Upload</span>
        </a>
        <ul class="dropdown-menu border-0 rounded-4 p-2 bg-light" aria-labelledby="uploadDropdown">
            <li>
                <a class="dropdown-item d-flex align-items-center rounded-3" href="#" id="openUploadModal">
                    <i class="ph ph-file-arrow-up me-2"></i> Upload File
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center rounded-3" href="#" id="openUploadFolderModal">
                    <i class="ph ph-folder-simple-plus me-2"></i> Upload Folder
                </a>
            </li>
        </ul>
    </div>
    <!-- Menu -->
    <ul class="nav nav-pills mt-2">
        <li class="nav-item  w-100 ">
            <a href="{{ route('dashboard') }}"
                class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ph ph-house pe-2 fs-5"></i>
                <span class="sidebar-text fw-normal">Home</span>
            </a>
        </li>
        <li class="nav-item w-100">
            <a href="{{ route('last') }}"
                class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('last') ? 'active' : '' }}">
                <i class="ph ph-clock-counter-clockwise me-2 fs-5"></i>
                <span class="sidebar-text">Last Opened</span>
            </a>
        </li>
        <li class="nav-item w-100">
            <a href="{{ route('myspace') }}"
                class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('myspace') ? 'active' : '' }}">
                <i class="ph ph-folder-user me-2 fs-5"></i>
                <span class="sidebar-text">My Space</span>
            </a>
        </li>
        <li class="nav-item w-100">
            <hr>
        </li>
        <li class="nav-item w-100">
            <a href="{{ route('shared') }}"
                class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('shared') ? 'active' : '' }}">
                <i class="ph ph-users-three me-2 fs-5"></i>
                <span class="sidebar-text">Shared With Me</span>
            </a>
        </li>
    </ul>

    <!-- Footer -->
    <div class="mt-auto">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none" id="logoutLink">
            <i class="ph ph-sign-out me-2 fs-5"></i>
            <span class="sidebar-text">Log Out</span>
        </a>

        <!-- Hidden Logout Form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
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

                    <!-- Drop Zone -->
                    <div id="uploadArea" class="upload-box text-center mb-4 border border-2 border-dashed rounded-4 p-4"
                        style="border-color: #dee2e6; cursor: pointer; transition: all 0.2s ease;">
                        <input type="file" name="file" id="fileInput" class="d-none" multiple
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
                    <!-- Label Input -->
                   <!-- Label Input -->
<div class="mb-4">
    <label class="form-label fw-semibold">Label</label>

    <!-- Container untuk existing labels dan tombol/input -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <!-- Daftar label yang tersedia -->
        <div id="existingLabels" class="d-flex flex-wrap gap-2">
            <!-- Label akan diisi via JavaScript -->
        </div>

        <!-- Tombol Add Label (akan berubah jadi input) -->
        <div id="addLabelContainer">
            <button type="button" class="btn btn-outline-primary rounded-3 d-flex align-items-center"
                id="addLabelBtn">
                <i class="ph ph-plus me-2"></i> Add Label
            </button>

            <!-- Input Label Baru (hidden default) -->
            <div id="newLabelContainer" class="d-none">
                <div class="input-group">
                    <input type="text" id="newLabelInput" class="form-control rounded-3 mx-2"
                        placeholder="Enter new label name" style="width: 150px;">
                    <button type="button" class="btn btn-blue rounded-3 me-2 small" style="size: 12px" id="saveNewLabelBtn">
                        Save
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-3 me-2 small" style="size: 12px"
                        id="cancelNewLabelBtn">
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
                    <img src="{{ asset('storage/images/folder.svg') }}" alt="Folder Icon" style="width: 80px;">
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

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmationModal" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background-color: #fff; max-width: 500px;">

            <!-- Close Button (Kanan Atas) -->
            <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal"
                aria-label="Close" style="z-index: 10; font-size: 1.1rem; opacity: 0.7;">
            </button>

            <div class="modal-body py-4 px-4">
                <!-- Teks Rata Kiri + Padding Kiri -->
                <div style="padding-left: 1.75rem;">
                    <h5 class="fw-bold mb-2 text-start" style="font-size: 1.25rem;">
                        Are you sure want to leave?
                    </h5>
                    <p class="text-muted small mb-4 text-start"
                        style="font-family: Rubik; line-height: 1.5; font-size: 0.875rem;">
                        You must log in again to access this dashboard.
                    </p>
                </div>

                <!-- Tombol: Cancel (kiri) + Log out (kanan) -->
                <div class="d-flex justify-content-between align-items-center"
                    style="padding-left: 1.75rem; padding-right: 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary rounded-4 px-4 py-2"
                        data-bs-dismiss="modal"
                        style="min-width: 100px; font-size: 14px;border-color: #d1d5db; color: #6b7280;">
                        Cancel
                    </button>
                    <button type="button" id="confirmLogoutBtn"
                        class="btn btn-danger rounded-4 px-4 py-2 text-white"
                        style="min-width: 100px; font-size:14px; background-color: #dc3545; border: none;">
                        Log out
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ==================== GLOBAL VARIABLES ====================
    let selectedLabelIds = []; // Global variable untuk menyimpan label yang dipilih
    let allLabels = [];

    // ==================== SIDEBAR TOGGLE FUNCTIONALITY ====================
    document.addEventListener("DOMContentLoaded", () => {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        const contentArea = document.querySelector('.sidebar-collapse-content');
        const texts = document.querySelectorAll('.sidebar-text');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            contentArea.classList.toggle('collapsed');

            if (sidebar.classList.contains('collapsed')) {
                texts.forEach(t => {
                    t.classList.add('opacity-0');
                    setTimeout(() => t.classList.add('d-none'), 200);
                });
            } else {
                texts.forEach(t => {
                    t.classList.remove('d-none');
                    setTimeout(() => t.classList.remove('opacity-0'), 50);
                });
            }
        });
    });

    // ==================== MODAL OPENERS ====================
    document.getElementById("openUploadFolderModal").addEventListener("click", function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById("uploadFolderModal"));

        // Reset form
        document.getElementById("createFolderForm").reset();
        document.getElementById("folderMessage").classList.add("d-none");

        modal.show();
    });

    document.getElementById("openUploadModal").addEventListener("click", function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById("uploadFileModal"));

        // Reset form dan labels
        document.getElementById("uploadForm").reset();
        document.getElementById("fileName").textContent = "";

        // Reset selected labels
        selectedLabelIds = [];
        const selectedLabelsContainer = document.getElementById("selectedLabels");
        if (selectedLabelsContainer) {
            selectedLabelsContainer.innerHTML = "";
        }

        modal.show();
    });

    // ==================== LOGOUT FUNCTIONALITY ====================
    document.addEventListener("DOMContentLoaded", function() {
        const logoutLink = document.getElementById('logoutLink');
        const logoutForm = document.getElementById('logout-form');
        const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');

        // Buka modal saat klik "Log Out"
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('logoutConfirmationModal'));
            modal.show();
        });

        // Konfirmasi logout → submit form
        confirmLogoutBtn.addEventListener('click', function() {
            logoutForm.submit();
        });

        document.getElementById('logoutConfirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                bootstrap.Modal.getInstance(this).hide();
            }
        });
    });

    // ==================== LABEL MANAGEMENT FUNCTIONALITY ====================
    document.addEventListener("DOMContentLoaded", async () => {
        const token = localStorage.getItem("token");
        const existingLabelsContainer = document.getElementById("existingLabels");
        const selectedLabelsContainer = document.getElementById("selectedLabels");
        const addLabelBtn = document.getElementById("addLabelBtn");
        const newLabelContainer = document.getElementById("newLabelContainer");
        const newLabelInput = document.getElementById("newLabelInput");
        const saveNewLabelBtn = document.getElementById("saveNewLabelBtn");
        const cancelNewLabelBtn = document.getElementById("cancelNewLabelBtn");

        // 🎨 Warna background untuk label
        const labelColors = [
            "FDDCD9", "EBE0D9", "FDE9DD", "EFEAFF", "FCF9DE",
            "E4F3FE", "FCE7ED", "E6E5E3", "EEFEF1", "F0EFED"
        ];

        // 🎨 Map background → text color
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

        // 🟢 Ambil semua label dari API
        async function loadLabels() {
            try {
                const res = await fetch("http://pdu-dms.my.id/api/labels", {
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                });
                const data = await res.json();
                allLabels = data.data || [];

                renderExistingLabels();
                renderSelectedLabels();
            } catch (err) {
                console.error("Gagal memuat label:", err);
            }
        }

        // Render daftar label yang tersedia
        function renderExistingLabels() {
            existingLabelsContainer.innerHTML = "";

            allLabels.forEach(label => {
                const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
                const textColor = colorMap[label.color] || "#333";

                const labelElement = document.createElement("button");
                labelElement.type = "button";
                labelElement.classList.add("btn", "rounded-pill", "px-3", "py-2", "small",
                    "border-0");
                labelElement.textContent = label.name;

                labelElement.style.backgroundColor = bgColor;
                labelElement.style.color = textColor;
                labelElement.style.border = `1px solid ${textColor}22 !important`;
                labelElement.style.cursor = "pointer";
                labelElement.style.transition = "all 0.2s ease";

                // Hover effects
                labelElement.addEventListener("mouseenter", () => {
                    labelElement.style.opacity = "0.8";
                    labelElement.style.transform = "scale(1.05)";
                });
                labelElement.addEventListener("mouseleave", () => {
                    labelElement.style.opacity = "1";
                    labelElement.style.transform = "scale(1)";
                });

                // Click handler untuk memilih label
                labelElement.addEventListener("click", () => {
                    if (!selectedLabelIds.includes(label.id)) {
                        selectedLabelIds.push(label.id);
                        renderSelectedLabels();
                    }
                });

                existingLabelsContainer.appendChild(labelElement);
            });
        }

        // Render label yang sudah dipilih
        function renderSelectedLabels() {
            selectedLabelsContainer.innerHTML = "";

            selectedLabelIds.forEach(labelId => {
                const label = allLabels.find(l => l.id === labelId);
                if (label) {
                    const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
                    const textColor = colorMap[label.color] || "#333";

                    const labelElement = document.createElement("div");
                    labelElement.classList.add("d-flex", "align-items-center", "gap-2",
                        "rounded-pill", "px-3", "py-2", "small");
                    labelElement.style.backgroundColor = bgColor;
                    labelElement.style.color = textColor;
                    labelElement.style.border = `1px solid ${textColor}22`;

                    const labelText = document.createElement("span");
                    labelText.textContent = label.name;

                    const removeBtn = document.createElement("button");
                    removeBtn.type = "button";
                    removeBtn.classList.add("btn", "btn-sm", "p-0", "shadow-none");
                    removeBtn.innerHTML =
                        '<i class="ph ph-x shadow-none" style="font-size: 12px;"></i>';
                    removeBtn.style.color = textColor;
                    removeBtn.style.border = "none";
                    removeBtn.style.background = "none";
                    removeBtn.style.cursor = "pointer";

                    removeBtn.addEventListener("click", () => {
                        selectedLabelIds = selectedLabelIds.filter(id => id !== labelId);
                        renderSelectedLabels();
                    });

                    labelElement.appendChild(labelText);
                    labelElement.appendChild(removeBtn);
                    selectedLabelsContainer.appendChild(labelElement);
                }
            });
        }

        // 🔘 Klik tombol Add → ganti dengan input
addLabelBtn.addEventListener("click", () => {
    addLabelBtn.classList.add("d-none");
    newLabelContainer.classList.remove("d-none");
    newLabelInput.focus();
});

// 💾 Simpan label baru
saveNewLabelBtn.addEventListener("click", async () => {
    const name = newLabelInput.value.trim();
    if (!name) {
        alert("Label name cannot be empty");
        return;
    }

    const color = labelColors[Math.floor(Math.random() * labelColors.length)];

    try {
        const res = await fetch("http://pdu-dms.my.id/api/create-label", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({
                name,
                color
            })
        });
        const result = await res.json();

        if (!res.ok) throw new Error(result.message || "Failed to create label");

        // Refresh labels
        await loadLabels();

        // Otomatis pilih label yang baru dibuat
        if (result.data && result.data.id) {
            selectedLabelIds.push(result.data.id);
            renderSelectedLabels();
        }

        // Kembalikan ke tombol Add
        resetAddLabelForm();

    } catch (err) {
        alert("Failed to create label: " + err.message);
    }
});

// ❌ Cancel pembuatan label baru
cancelNewLabelBtn.addEventListener("click", () => {
    resetAddLabelForm();
});

// Fungsi untuk reset form add label
function resetAddLabelForm() {
    newLabelInput.value = "";
    newLabelContainer.classList.add("d-none");
    addLabelBtn.classList.remove("d-none");
}

// Submit dengan Enter di input label baru
newLabelInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
        e.preventDefault();
        saveNewLabelBtn.click();
    }
});

        // Jalankan load awal
        loadLabels();
    });

    // ==================== UPLOAD FILE FUNCTIONALITY ====================
    const uploadArea = document.getElementById("uploadArea");
    const fileInput = document.getElementById("fileInput");
    const fileNameDisplay = document.getElementById("fileName");

    // Klik area upload → buka file picker
    uploadArea.addEventListener("click", () => fileInput.click());

    // Preview nama file
    fileInput.addEventListener("change", () => {
        if (fileInput.files.length > 0) {
            const fileNames = Array.from(fileInput.files).map(f => f.name).join(", ");
            fileNameDisplay.textContent = fileNames;
        }
    });

    // Efek drag & drop
    uploadArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = "#0d6efd";
        uploadArea.style.backgroundColor = "rgba(13, 110, 253, 0.05)";
    });

    uploadArea.addEventListener("dragleave", () => {
        uploadArea.style.borderColor = "#dee2e6";
        uploadArea.style.backgroundColor = "transparent";
    });

    uploadArea.addEventListener("drop", (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = "#dee2e6";
        uploadArea.style.backgroundColor = "transparent";

        fileInput.files = e.dataTransfer.files;
        const fileNames = Array.from(fileInput.files).map(f => f.name).join(", ");
        fileNameDisplay.textContent = fileNames;
    });

    // Submit form upload file
    document.getElementById("uploadForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const formData = new FormData();
        const files = fileInput.files;
        const parentId = getParentIdFromUrl();

        if (files.length === 0) {
            alert("Please select at least one file to upload");
            return;
        }

        for (let file of files) {
            formData.append("files[]", file);
            formData.append("relative_paths[]", file.name);
        }

        if (parentId) formData.append("parent_id", parentId);

        const title = document.getElementById("title").value;
        if (title) formData.append("title", title);

        // ✅ PERBAIKAN: Kirim labels[] sebagai array (sesuai Postman)
        // Sekarang selectedLabelIds bisa diakses karena di global scope
        if (selectedLabelIds.length > 0) {
            selectedLabelIds.forEach(labelId => {
                formData.append("labels[]", labelId);
            });
        }

        const token = localStorage.getItem("token");

        try {
            const res = await fetch("http://pdu-dms.my.id/api/upload-files", {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + token
                },
                body: formData
            });
            const result = await res.json();
            if (!res.ok) throw new Error(result.message || "Failed to upload files");

            alert("Files uploaded successfully!");
            bootstrap.Modal.getInstance(document.getElementById("uploadFileModal")).hide();
            location.reload();
        } catch (err) {
            alert("Failed to upload: " + err.message);
        }
    });

    // ==================== CREATE FOLDER FUNCTIONALITY ====================

    // Fungsi untuk mendapatkan parent_id dari URL
    function getParentIdFromUrl() {
        const currentUrl = window.location.href;
        const urlParts = currentUrl.split('/');

        // Cari bagian 'myspace' dalam URL
        const myspaceIndex = urlParts.indexOf('myspace');

        if (myspaceIndex !== -1 && urlParts.length > myspaceIndex + 1) {
            const potentialId = urlParts[myspaceIndex + 1];

            // Validasi apakah ini angka (ID folder)
            if (potentialId && !isNaN(potentialId) && potentialId.trim() !== '') {
                return parseInt(potentialId);
            }
        }

        // Jika tidak ada ID setelah 'myspace', return null (root folder)
        return null;
    }

    // Fungsi untuk membuat folder baru
    document.getElementById("createFolderBtn").addEventListener("click", async function() {
        const folderName = document.getElementById("folderName").value.trim();
        const messageDiv = document.getElementById("folderMessage");
        const token = localStorage.getItem("token");

        // Validasi input
        if (!folderName) {
            showMessage("Please enter a folder name", "danger");
            return;
        }

        // Dapatkan parent_id dari URL
        const parentId = getParentIdFromUrl();

        // Siapkan data untuk API
        const folderData = {
            name: folderName
        };

        // Tambahkan parent_id jika ada
        if (parentId) {
            folderData.parent_id = parentId;
        }

        try {
            // Tampilkan loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

            // Kirim request ke API
            const res = await fetch("http://pdu-dms.my.id/api/create-folder", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + token
                },
                body: JSON.stringify(folderData)
            });

            const result = await res.json();

            if (res.ok) {
                // Success
                showMessage("Folder created successfully!", "success");

                // Reset form setelah beberapa detik dan tutup modal
                setTimeout(() => {
                    document.getElementById("createFolderForm").reset();
                    bootstrap.Modal.getInstance(document.getElementById("uploadFolderModal"))
                .hide();

                    // Refresh halaman untuk menampilkan folder baru
                    location.reload();
                }, 1500);
            } else {
                // Error dari server
                throw new Error(result.message || "Failed to create folder");
            }
        } catch (err) {
            console.error("Error creating folder:", err);
            showMessage("Failed to create folder: " + err.message, "danger");
        } finally {
            // Reset button state
            this.disabled = false;
            this.innerHTML = "Create Folder";
        }
    });

    // Fungsi untuk menampilkan pesan feedback
    function showMessage(message, type) {
        const messageDiv = document.getElementById("folderMessage");
        messageDiv.textContent = message;
        messageDiv.className = `alert alert-${type} mt-3`;
        messageDiv.classList.remove("d-none");

        // Auto hide setelah 5 detik untuk pesan success
        if (type === "success") {
            setTimeout(() => {
                messageDiv.classList.add("d-none");
            }, 5000);
        }
    }

    // Submit form dengan tombol Enter
    document.getElementById("folderName").addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            document.getElementById("createFolderBtn").click();
        }
    });
</script>
