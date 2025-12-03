<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMS PDU')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/css/icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>
</head>

@if (session('token'))
    <script>
        localStorage.setItem('token', '{{ session('token') }}');
    </script>
@endif

<body class="d-flex vh-100 overflow-hidden">

    <!-- Sidebar Desktop -->
    <div class="position-fixed h-100 z-3 d-none d-lg-block">
        @include('partials.sidebar')
    </div>

    <!-- Navbar Mobile -->
    <div class="d-block d-lg-none w-100 position-fixed top-0 start-0 z-3">
        @include('partials.navbar-mobile')
    </div>

    <!-- Konten -->
    <div
        class="d-flex flex-column flex-grow-1 sidebar-collapse-content h-100 overflow-auto w-100 content-wrapper p-4 mt-lg-0 mt-5">
        {{-- Navbar desktop muncul di atas konten --}}
        <div class="d-none d-lg-block mb-3">
            @include('partials.navbar')
        </div>
        <div class="content-wrapper">
            @yield('content')
        </div>

        @yield('modals')



    </div>

<div class="d-block d-lg-none">
    <div class="btn-group dropup position-fixed floating-btn-wrapper">
        <button class="btn btn-float rounded-circle shadow floating-btn"
                data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ph-bold ph-plus fs-5"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end border-0 rounded-4 p-2 shadow-lg">
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#" id="mobileOpenUploadModal">
                    <i class="ph ph-file-arrow-up me-2"></i> Upload File
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="#" id="mobileOpenUploadFolderModal">
                    <i class="ph ph-folder-simple-plus me-2"></i> Upload Folder
                </a>
            </li>
        </ul>
    </div>
</div>


</body>

    <!-- JS -->
    {{-- <div class="position-fixed h-100 z-3">

    <body class="d-flex vh-100 overflow-hidden">
        {{-- <div class="position-fixed h-100 z-3">
        @include('partials.sidebar')
    </div> --}}


        {{-- <div class="d-flex flex-column flex-grow-1 sidebar-collapse-content h-100 overflow-auto p-4">
        {{-- <div class="d-flex flex-column flex-grow-1 sidebar-collapse-content h-100 overflow-auto p-4">
        @yield('content')
    </div> --}}
    <!--Icons-->
    <!-- JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{ asset('js/myspace.js') }}"></script>

    <script>

        // ==================== GLOBAL VARIABLES ====================
    let selectedLabelIds = []; // Untuk upload modal
    let allLabels = []; // Semua labels dari API
    let editSelectedLabelIds = []; // Untuk edit modal

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

    // ==================== GLOBAL FUNCTIONS ====================

    // Fungsi untuk mendapatkan token
    function getToken() {
        return "{{ session('token') }}";
    }

    // Fungsi untuk menampilkan pesan feedback
    function showMessage(message, type, containerId = null) {
        let messageDiv;

        if (containerId) {
            messageDiv = document.getElementById(containerId);
        } else {
            // Default container untuk modal yang sedang aktif
            const activeModal = document.querySelector('.modal.show');
            if (activeModal) {
                messageDiv = activeModal.querySelector('.alert');
                if (!messageDiv) {
                    // Buat elemen alert jika belum ada
                    messageDiv = document.createElement('div');
                    messageDiv.className = 'alert d-none';
                    activeModal.querySelector('.modal-body').prepend(messageDiv);
                }
            } else {
                // Fallback ke alert biasa jika tidak ada modal aktif
                alert(message);
                return;
            }
        }

        if (!messageDiv) {
            console.error('Message container not found');
            return;
        }

        messageDiv.textContent = message;
        messageDiv.className = `alert alert-${type} mt-3`;
        messageDiv.classList.remove("d-none");

        // Auto-hide untuk pesan sukses
        if (type === "success") {
            setTimeout(() => {
                messageDiv.classList.add("d-none");
            }, 5000);
        }

        // Scroll ke pesan
        messageDiv.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    // 🟢 Ambil semua label dari API
    async function loadLabels() {
        const token = getToken();
        if (!token) {
            console.error('No token available');
            return;
        }

        try {
            const res = await fetch("https://pdu-dms.my.id/api/labels", {
                headers: {
                    "Authorization": "Bearer " + token
                }
            });

            if (!res.ok) {
                if (res.status === 401) {
                    window.location.href = "{{ route('signin') }}";
                    return;
                }
                throw new Error(`HTTP ${res.status}`);
            }

            const data = await res.json();
            allLabels = data.data || [];

            // Render labels untuk upload modal
            renderExistingLabels();
            renderSelectedLabels();

            // Render labels untuk edit modal
            if (window.renderEditExistingLabels) {
                window.renderEditExistingLabels();
            }

            console.log('Labels loaded successfully:', allLabels.length);

        } catch (err) {
            console.error("Gagal memuat label:", err);
        }
    }

    // Render daftar label yang tersedia (UPLOAD MODAL)
    // Render daftar label yang tersedia (UPLOAD MODAL) dengan delete button
// Render daftar label yang tersedia (UPLOAD MODAL) dengan delete button di dalam badge
function renderExistingLabels() {
    const existingLabelsContainer = document.getElementById("existingLabels");
    if (!existingLabelsContainer) return;

    existingLabelsContainer.innerHTML = "";

    allLabels.forEach(label => {
        const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
        const textColor = colorMap[label.color] || "#333";

        // Buat container untuk label + delete button dalam satu badge
        const labelContainer = document.createElement("div");
        labelContainer.classList.add("d-inline-flex", "align-items-center", "rounded-pill", "px-3", "py-2", "small", "me-2", "mb-2");
        labelContainer.style.backgroundColor = bgColor;
        labelContainer.style.color = textColor;
        labelContainer.style.border = `1px solid ${textColor}22`;
        labelContainer.style.cursor = "pointer";
        labelContainer.style.transition = "all 0.2s ease";

        // Hover effects untuk seluruh container
        labelContainer.addEventListener("mouseenter", () => {
            labelContainer.style.opacity = "0.9";
            labelContainer.style.transform = "scale(1.02)";
        });
        labelContainer.addEventListener("mouseleave", () => {
            labelContainer.style.opacity = "1";
            labelContainer.style.transform = "scale(1)";
        });

        // Text label
        const labelText = document.createElement("span");
        labelText.textContent = label.name;
        labelText.classList.add("me-1");

        // Delete button (dropdown) - lebih kecil dan compact
        const deleteDropdown = document.createElement("div");
        deleteDropdown.classList.add("dropdown");

        deleteDropdown.innerHTML = `
            <button class="btn btn-link p-0 shadow-none"
                    data-bs-toggle="dropdown"
                    data-bs-display="static"
                    style="font-size: 0.6rem; color: ${textColor}; line-height: 1;">
                <i class="ph ph-dots-three-vertical"></i>
            </button>
            <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger delete-existing-label-btn"
                       href="#"
                       data-label-id="${label.id}"
                       data-label-name="${label.name}">
                        <i class="ph ph-trash fs-5"></i> Delete Label
                    </a>
                </li>
            </ul>
        `;

        // Click handler untuk memilih label (klik area text)
        labelText.addEventListener("click", (e) => {
            e.stopPropagation();
            if (!selectedLabelIds.includes(label.id)) {
                selectedLabelIds.push(label.id);
                renderSelectedLabels();
            }
        });

        // Click handler untuk container (fallback)
        labelContainer.addEventListener("click", (e) => {
            if (!e.target.closest('.dropdown')) {
                if (!selectedLabelIds.includes(label.id)) {
                    selectedLabelIds.push(label.id);
                    renderSelectedLabels();
                }
            }
        });

        labelContainer.appendChild(labelText);
        labelContainer.appendChild(deleteDropdown);
        existingLabelsContainer.appendChild(labelContainer);
    });
}

    // Render label yang sudah dipilih (UPLOAD MODAL)
    function renderSelectedLabels() {
        const selectedLabelsContainer = document.getElementById("selectedLabels");
        if (!selectedLabelsContainer) return;

        selectedLabelsContainer.innerHTML = "";

        selectedLabelIds.forEach(labelId => {
            const label = allLabels.find(l => l.id === labelId);
            if (label) {
                const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
                const textColor = colorMap[label.color] || "#333";

                const labelElement = document.createElement("div");
                labelElement.classList.add("d-flex", "align-items-center", "gap-2", "rounded-pill", "px-3",
                    "py-2", "small", "me-2", "mb-2");
                labelElement.style.backgroundColor = bgColor;
                labelElement.style.color = textColor;
                labelElement.style.border = `1px solid ${textColor}22`;

                const labelText = document.createElement("span");
                labelText.textContent = label.name;

                const removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.classList.add("btn", "btn-sm", "p-0", "shadow-none");
                removeBtn.innerHTML = '<i class="ph ph-x shadow-none" style="font-size: 12px;"></i>';
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
        document.getElementById("uploadMessage").classList.add("d-none");

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
        const token = getToken();

        if (!token) {
            console.error('No token available in session');
            window.location.href = "{{ route('signin') }}";
            return;
        }

        const addLabelBtn = document.getElementById("addLabelBtn");
        const newLabelContainer = document.getElementById("newLabelContainer");
        const newLabelInput = document.getElementById("newLabelInput");
        const saveNewLabelBtn = document.getElementById("saveNewLabelBtn");
        const cancelNewLabelBtn = document.getElementById("cancelNewLabelBtn");

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
                showMessage("Label name cannot be empty", "danger", "uploadMessage");
                return;
            }

            const color = labelColors[Math.floor(Math.random() * labelColors.length)];

            try {
                const res = await fetch("https://pdu-dms.my.id/api/create-label", {
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

                if (!res.ok) {
                    if (res.status === 401) {
                        window.location.href = "{{ route('signin') }}";
                        return;
                    }
                    throw new Error(`HTTP ${res.status}`);
                }

                const result = await res.json();

                if (result.success === false) {
                    throw new Error(result.message || "Failed to create label");
                }

                // Refresh labels
                await loadLabels();

                // Otomatis pilih label yang baru dibuat
                if (result.data && result.data.id) {
                    selectedLabelIds.push(result.data.id);
                    renderSelectedLabels();
                }

                // Kembalikan ke tombol Add
                resetAddLabelForm();

                showMessage("Label created successfully!", "success", "uploadMessage");

            } catch (err) {
                showMessage("Failed to create label: " + err.message, "danger",
                "uploadMessage");
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
            const file = fileInput.files[0];
            fileNameDisplay.textContent = file.name;

            // Auto-fill title dengan nama file tanpa extension
            const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
            document.getElementById("title").value = fileNameWithoutExt;
        } else {
            fileNameDisplay.textContent = "";
            document.getElementById("title").value = "";
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

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            fileNameDisplay.textContent = file.name;

            // Auto-fill title dengan nama file tanpa extension
            const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
            document.getElementById("title").value = fileNameWithoutExt;
        }
    });

    // Submit form upload file
    document.getElementById("uploadForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const token = getToken();
        if (!token) {
            showMessage("Session expired. Please login again.", "danger", "uploadMessage");
            window.location.href = "{{ route('signin') }}";
            return;
        }

        const file = fileInput.files[0];
        const parentId = getParentIdFromUrl();

        if (!file) {
            showMessage("Please select a file to upload", "danger", "uploadMessage");
            return;
        }

        try {
            const formData = new FormData();
            const title = document.getElementById("title").value.trim();

            // Jika title diisi, gunakan sebagai custom filename
            if (title) {
                const originalExtension = file.name.split('.').pop();
                const customFileName = `${title}.${originalExtension}`;
                formData.append("files[]", file, customFileName);
                formData.append("relative_paths[]", customFileName);
            } else {
                formData.append("files[]", file);
                formData.append("relative_paths[]", file.name);
            }

            if (parentId) formData.append("parent_id", parentId);

            // Tambahkan labels jika ada
            if (selectedLabelIds.length > 0) {
                selectedLabelIds.forEach(labelId => {
                    formData.append("labels[]", labelId);
                });
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Tampilkan loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

            const res = await fetch("https://pdu-dms.my.id/api/upload-files", {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + token
                },
                body: formData
            });

            if (!res.ok) {
                if (res.status === 401) {
                    showMessage("Session expired. Please login again.", "danger", "uploadMessage");
                    window.location.href = "{{ route('signin') }}";
                    return;
                }
                throw new Error(`HTTP ${res.status}`);
            }

            const result = await res.json();
            if (!res.ok) throw new Error(result.message || "Failed to upload file");

            showMessage("File uploaded successfully!", "success", "uploadMessage");

            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById("uploadFileModal")).hide();
                location.reload();
            }, 1500);

        } catch (err) {
            showMessage("Failed to upload: " + err.message, "danger", "uploadMessage");

            // Reset button state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Upload';
        }
    });

    // ==================== CREATE FOLDER FUNCTIONALITY ====================

    // Fungsi untuk mendapatkan parent_id dari URL
    function getParentIdFromUrl() {
        const currentUrl = window.location.href;
        const urlParts = currentUrl.split('/');

        const myspaceIndex = urlParts.indexOf('myspace');
        if (myspaceIndex !== -1 && urlParts.length > myspaceIndex + 1) {
            const potentialId = urlParts[myspaceIndex + 1];
            if (potentialId && !isNaN(potentialId) && potentialId.trim() !== '') {
                return parseInt(potentialId);
            }
        }

        return null;
    }

    // Fungsi untuk membuat folder baru
    document.getElementById("createFolderBtn").addEventListener("click", async function() {
        const token = getToken();
        if (!token) {
            showMessage("Session expired. Please login again.", "danger", "folderMessage");
            window.location.href = "{{ route('signin') }}";
            return;
        }

        const folderName = document.getElementById("folderName").value.trim();
        const messageDiv = document.getElementById("folderMessage");

        if (!folderName) {
            showMessage("Please enter a folder name", "danger", "folderMessage");
            return;
        }

        const parentId = getParentIdFromUrl();

        const folderData = {
            name: folderName
        };

        if (parentId) {
            folderData.parent_id = parentId;
        }

        try {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

            const res = await fetch("https://pdu-dms.my.id/api/create-folder", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + token
                },
                body: JSON.stringify(folderData)
            });

            if (!res.ok) {
                if (res.status === 401) {
                    showMessage("Session expired. Please login again.", "danger", "folderMessage");
                    window.location.href = "{{ route('signin') }}";
                    return;
                }
                throw new Error(`HTTP ${res.status}`);
            }

            const result = await res.json();

            if (res.ok) {
                showMessage("Folder created successfully!", "success", "folderMessage");

                setTimeout(() => {
                    document.getElementById("createFolderForm").reset();
                    bootstrap.Modal.getInstance(document.getElementById("uploadFolderModal"))
                .hide();
                    location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || "Failed to create folder");
            }
        } catch (err) {
            console.error("Error creating folder:", err);
            showMessage("Failed to create folder: " + err.message, "danger", "folderMessage");
        } finally {
            this.disabled = false;
            this.innerHTML = "Create Folder";
        }
    });

    // Submit form dengan tombol Enter
    document.getElementById("folderName").addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            document.getElementById("createFolderBtn").click();
        }
    });

    // ==================== EDIT FILE FUNCTIONALITY ====================

    // Global function untuk membuka modal edit dari mana saja
    window.openEditModal = async function(fileId, fileName, fileLabels) {
        // Set data file ke form
        document.getElementById('editFileId').value = fileId;
        document.getElementById('editTitle').value = fileName.replace(/\.[^/.]+$/, ""); // Remove extension
        document.getElementById('editFileNameDisplay').textContent = fileName;

        // Set file info
        const fileExtension = fileName.split('.').pop().toUpperCase();
        document.getElementById('editFileInfo').textContent = `${fileExtension} File`;

        // Set labels yang sudah ada
        editSelectedLabelIds = fileLabels.map(label => label.id);

        // Pastikan labels sudah di-load
        if (allLabels.length === 0) {
            await loadLabels();
        }

        renderEditSelectedLabels();

        // Buka modal
        const modal = new bootstrap.Modal(document.getElementById('editFileModal'));
        modal.show();
    };

    // Initialize edit functionality
    function initializeEditFunctionality() {
        // Event delegation untuk tombol edit file di seluruh aplikasi
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-file-btn')) {
                e.preventDefault();
                const button = e.target.closest('.edit-file-btn');
                const fileId = button.getAttribute('data-id');
                const fileName = button.getAttribute('data-name');
                const fileLabels = JSON.parse(button.getAttribute('data-labels') || '[]');

                window.openEditModal(fileId, fileName, fileLabels);
            }
        });

        initializeEditLabels();
    }

    function initializeEditLabels() {
        const editExistingLabelsContainer = document.getElementById("editExistingLabels");
        const editSelectedLabelsContainer = document.getElementById("editSelectedLabels");
        const editAddLabelBtn = document.getElementById("editAddLabelBtn");
        const editNewLabelContainer = document.getElementById("editNewLabelContainer");
        const editNewLabelInput = document.getElementById("editNewLabelInput");
        const editSaveNewLabelBtn = document.getElementById("editSaveNewLabelBtn");
        const editCancelNewLabelBtn = document.getElementById("editCancelNewLabelBtn");

        // Render daftar label yang tersedia untuk edit
        // Render daftar label yang tersedia untuk edit dengan delete button
// Render daftar label yang tersedia untuk edit dengan delete button di dalam badge
function renderEditExistingLabels() {
    const editExistingLabelsContainer = document.getElementById("editExistingLabels");
    if (!editExistingLabelsContainer) return;

    editExistingLabelsContainer.innerHTML = "";

    if (!allLabels || allLabels.length === 0) {
        editExistingLabelsContainer.innerHTML = `
            <div class="text-muted small">No labels available. Create one first.</div>
        `;
        return;
    }

    allLabels.forEach(label => {
        const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
        const textColor = colorMap[label.color] || "#333";

        // Buat container untuk label + delete button dalam satu badge
        const labelContainer = document.createElement("div");
        labelContainer.classList.add("d-inline-flex", "align-items-center", "rounded-pill", "px-3", "py-2", "small", "me-2", "mb-2");
        labelContainer.style.backgroundColor = bgColor;
        labelContainer.style.color = textColor;
        labelContainer.style.border = `1px solid ${textColor}22`;
        labelContainer.style.cursor = "pointer";
        labelContainer.style.transition = "all 0.2s ease";

        // Jika label sudah dipilih, beri style berbeda
        if (editSelectedLabelIds.includes(label.id)) {
            labelContainer.style.opacity = "0.7";
            labelContainer.style.border = `2px solid ${textColor}`;
        }

        // Hover effects untuk seluruh container
        labelContainer.addEventListener("mouseenter", () => {
            labelContainer.style.opacity = "0.9";
            labelContainer.style.transform = "scale(1.02)";
        });
        labelContainer.addEventListener("mouseleave", () => {
            if (!editSelectedLabelIds.includes(label.id)) {
                labelContainer.style.opacity = "1";
                labelContainer.style.transform = "scale(1)";
            } else {
                labelContainer.style.opacity = "0.7";
                labelContainer.style.transform = "scale(1)";
            }
        });

        // Text label
        const labelText = document.createElement("span");
        labelText.textContent = label.name;
        labelText.classList.add("me-1");

        // Delete button (dropdown) - lebih kecil dan compact
        const deleteDropdown = document.createElement("div");
        deleteDropdown.classList.add("dropdown");

        deleteDropdown.innerHTML = `
            <button class="btn btn-link p-0 shadow-none"
                    data-bs-toggle="dropdown"
                    data-bs-display="static"
                    style="font-size: 0.6rem; color: ${textColor}; line-height: 1;">
                <i class="ph ph-dots-three-vertical"></i>
            </button>
            <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger delete-existing-label-btn"
                       href="#"
                       data-label-id="${label.id}"
                       data-label-name="${label.name}">
                        <i class="ph ph-trash fs-5"></i> Delete Label
                    </a>
                </li>
            </ul>
        `;

        // Click handler untuk memilih label (klik area text)
        labelText.addEventListener("click", (e) => {
            e.stopPropagation();
            if (!editSelectedLabelIds.includes(label.id)) {
                editSelectedLabelIds.push(label.id);
                renderEditSelectedLabels();
                renderEditExistingLabels();
            } else {
                editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== label.id);
                renderEditSelectedLabels();
                renderEditExistingLabels();
            }
        });

        // Click handler untuk container (fallback)
        labelContainer.addEventListener("click", (e) => {
            if (!e.target.closest('.dropdown')) {
                if (!editSelectedLabelIds.includes(label.id)) {
                    editSelectedLabelIds.push(label.id);
                    renderEditSelectedLabels();
                    renderEditExistingLabels();
                } else {
                    editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== label.id);
                    renderEditSelectedLabels();
                    renderEditExistingLabels();
                }
            }
        });

        labelContainer.appendChild(labelText);
        labelContainer.appendChild(deleteDropdown);
        editExistingLabelsContainer.appendChild(labelContainer);
    });
}

        // Render label yang sudah dipilih di modal edit
        function renderEditSelectedLabels() {
            if (!editSelectedLabelsContainer) return;

            editSelectedLabelsContainer.innerHTML = "";

            if (editSelectedLabelIds.length === 0) {
                editSelectedLabelsContainer.innerHTML = `
                    <div class="text-muted small">No labels selected</div>
                `;
                return;
            }

            editSelectedLabelIds.forEach(labelId => {
                const label = allLabels.find(l => l.id === labelId);
                if (label) {
                    const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
                    const textColor = colorMap[label.color] || "#333";

                    const labelElement = document.createElement("div");
                    labelElement.classList.add("d-flex", "align-items-center", "gap-2", "rounded-pill", "px-3",
                        "py-2", "small", "me-2", "mb-2");
                    labelElement.style.backgroundColor = bgColor;
                    labelElement.style.color = textColor;
                    labelElement.style.border = `1px solid ${textColor}22`;

                    const labelText = document.createElement("span");
                    labelText.textContent = label.name;

                    const removeBtn = document.createElement("button");
                    removeBtn.type = "button";
                    removeBtn.classList.add("btn", "btn-sm", "p-0", "shadow-none");
                    removeBtn.innerHTML = '<i class="ph ph-x shadow-none" style="font-size: 12px;"></i>';
                    removeBtn.style.color = textColor;
                    removeBtn.style.border = "none";
                    removeBtn.style.background = "none";
                    removeBtn.style.cursor = "pointer";

                    removeBtn.addEventListener("click", (e) => {
                        e.stopPropagation();
                        editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== labelId);
                        renderEditSelectedLabels();
                        renderEditExistingLabels();
                    });

                    labelElement.appendChild(labelText);
                    labelElement.appendChild(removeBtn);
                    editSelectedLabelsContainer.appendChild(labelElement);
                }
            });
        }

        // 🔘 Klik tombol Add → ganti dengan input
        if (editAddLabelBtn) {
            editAddLabelBtn.addEventListener("click", () => {
                editAddLabelBtn.classList.add("d-none");
                editNewLabelContainer.classList.remove("d-none");
                editNewLabelInput.focus();
            });
        }

        // 💾 Simpan label baru di modal edit
        if (editSaveNewLabelBtn) {
            editSaveNewLabelBtn.addEventListener("click", async () => {
                const name = editNewLabelInput.value.trim();
                if (!name) {
                    showMessage("Label name cannot be empty", "danger", "editFileMessage");
                    return;
                }

                // Cek apakah label sudah ada
                const existingLabel = allLabels.find(l => l.name.toLowerCase() === name.toLowerCase());
                if (existingLabel) {
                    showMessage("Label with this name already exists", "danger", "editFileMessage");
                    return;
                }

                const color = labelColors[Math.floor(Math.random() * labelColors.length)];

                try {
                    const token = getToken();
                    const res = await fetch("https://pdu-dms.my.id/api/create-label", {
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

                    if (!res.ok) {
                        if (res.status === 401) {
                            window.location.href = "{{ route('signin') }}";
                            return;
                        }
                        throw new Error(`HTTP ${res.status}`);
                    }

                    const result = await res.json();

                    if (result.success === false) {
                        throw new Error(result.message || "Failed to create label");
                    }

                    // Refresh labels
                    await loadLabels();

                    // Otomatis pilih label yang baru dibuat
                    if (result.data && result.data.id) {
                        editSelectedLabelIds.push(result.data.id);
                        renderEditSelectedLabels();
                        renderEditExistingLabels();
                    }

                    // Kembalikan ke tombol Add
                    resetEditAddLabelForm();

                    showMessage("Label created successfully!", "success", "editFileMessage");

                } catch (err) {
                    console.error('Error creating label:', err);
                    showMessage("Failed to create label: " + err.message, "danger", "editFileMessage");
                }
            });
        }

        // ❌ Cancel pembuatan label baru di modal edit
        if (editCancelNewLabelBtn) {
            editCancelNewLabelBtn.addEventListener("click", () => {
                resetEditAddLabelForm();
            });
        }

        // Fungsi untuk reset form add label di modal edit
        function resetEditAddLabelForm() {
            if (editNewLabelInput) editNewLabelInput.value = "";
            if (editNewLabelContainer) editNewLabelContainer.classList.add("d-none");
            if (editAddLabelBtn) editAddLabelBtn.classList.remove("d-none");
        }

        // Submit dengan Enter di input label baru
        if (editNewLabelInput) {
            editNewLabelInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    if (editSaveNewLabelBtn) {
                        editSaveNewLabelBtn.click();
                    }
                }
            });
        }

        // Export functions untuk digunakan elsewhere
        window.renderEditExistingLabels = renderEditExistingLabels;
        window.renderEditSelectedLabels = renderEditSelectedLabels;

        // Render initial labels
        renderEditExistingLabels();
    }

    // Submit form edit file
    document.getElementById("editFileForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const token = getToken();
        if (!token) {
            showMessage("Session expired. Please login again.", "danger", "editFileMessage");
            window.location.href = "{{ route('signin') }}";
            return;
        }

        const fileId = document.getElementById("editFileId").value;
        const title = document.getElementById("editTitle").value.trim();
        const originalFileName = document.getElementById("editFileNameDisplay").textContent;
        const fileExtension = originalFileName.split('.').pop();

        if (!title) {
            showMessage("Please enter a file name", "danger", "editFileMessage");
            return;
        }

        // Tambahkan extension jika user menghapusnya
        const finalFileName = title.includes('.') ? title : `${title}.${fileExtension}`;

        try {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Tampilkan loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            // Siapkan data sesuai format API
            const requestData = {
                name: finalFileName
            };

            // Tambahkan labels jika ada (format array of IDs)
            if (editSelectedLabelIds.length > 0) {
                requestData.labels = editSelectedLabelIds;
            }

            console.log('Sending update data:', requestData);
            console.log('File ID:', fileId);

            // GUNAKAN METHOD PATCH sesuai dokumentasi API
            const res = await fetch(`https://pdu-dms.my.id/api/update-file/${fileId}`, {
                method: "PATCH",
                headers: {
                    "Authorization": "Bearer " + token,
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(requestData)
            });

            console.log('Response status:', res.status);

            if (!res.ok) {
                let errorMessage = `HTTP ${res.status}`;
                try {
                    const errorData = await res.json();
                    errorMessage = errorData.message || errorMessage;
                } catch (parseError) {
                    console.error('Error parsing error response:', parseError);
                }
                throw new Error(errorMessage);
            }

            const result = await res.json();
            console.log('Update response:', result);

            if (result.success === false) {
                throw new Error(result.message || "Failed to update file");
            }

            showMessage("File updated successfully!", "success", "editFileMessage");

            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById("editFileModal"));
            modal.hide();

            // Refresh halaman untuk menampilkan perubahan
            setTimeout(() => {
                location.reload();
            }, 1000);

        } catch (err) {
            console.error('Error updating file:', err);
            showMessage("Failed to update file: " + err.message, "danger", "editFileMessage");

            // Reset button state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
        }
    });

    // Reset form ketika modal ditutup
    document.getElementById('editFileModal').addEventListener('hidden.bs.modal', function() {
        // Reset selected labels
        editSelectedLabelIds = [];

        // Reset form
        document.getElementById('editFileForm').reset();

        // Clear selected labels display
        const editSelectedLabelsContainer = document.getElementById("editSelectedLabels");
        if (editSelectedLabelsContainer) {
            editSelectedLabelsContainer.innerHTML = "";
        }

        // Reset add label form
        const editNewLabelContainer = document.getElementById("editNewLabelContainer");
        const editAddLabelBtn = document.getElementById("editAddLabelBtn");
        if (editNewLabelContainer && editAddLabelBtn) {
            editNewLabelContainer.classList.add("d-none");
            editAddLabelBtn.classList.remove("d-none");
        }
    });
    // Event listener untuk delete label dari existing labels
document.addEventListener('click', async (e) => {
    if (e.target.closest('.delete-existing-label-btn')) {
        e.preventDefault();
        e.stopPropagation();

        const deleteBtn = e.target.closest('.delete-existing-label-btn');
        const labelId = deleteBtn.getAttribute('data-label-id');
        const labelName = deleteBtn.getAttribute('data-label-name');

        if (!confirm(`Are you sure you want to delete label "${labelName}"?`)) {
            return;
        }

        await deleteExistingLabel(labelId, labelName, deleteBtn);
    }
});

// Function untuk delete label
async function deleteExistingLabel(labelId, labelName, buttonElement) {
    const token = getToken();
    if (!token) {
        showMessage("Session expired. Please login again.", "danger");
        window.location.href = "{{ route('signin') }}";
        return;
    }

    try {
        // Show loading state
        const originalText = buttonElement.innerHTML;
        buttonElement.innerHTML = '<i class="ph ph-spinner ph-spin fs-5"></i> Deleting...';
        buttonElement.disabled = true;

        const response = await fetch(`https://pdu-dms.my.id/api/delete-label/${labelId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok) {
            // Show success message
            showMessage(`Label "${labelName}" deleted successfully!`, "success");

            // Remove from selected labels if selected
            selectedLabelIds = selectedLabelIds.filter(id => id !== parseInt(labelId));
            editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== parseInt(labelId));

            // Reload labels
            await loadLabels();

            // Re-render selected labels
            renderSelectedLabels();
            if (window.renderEditSelectedLabels) {
                window.renderEditSelectedLabels();
            }
        } else {
            throw new Error(result.message || 'Failed to delete label');
        }

    } catch (error) {
        console.error('Delete label error:', error);
        showMessage('Failed to delete label: ' + error.message, "danger");
    } finally {
        // Reset button state
        if (buttonElement) {
            buttonElement.innerHTML = originalText;
            buttonElement.disabled = false;
        }
    }
}

    // ==================== INITIALIZATION ====================
    document.addEventListener("DOMContentLoaded", function() {
        initializeEditFunctionality();
    });

        document.addEventListener("DOMContentLoaded", function () {
    // Upload File dari floating button
    const mobileOpenUploadModal = document.getElementById("mobileOpenUploadModal");
    if (mobileOpenUploadModal) {
        mobileOpenUploadModal.addEventListener("click", function (e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById("uploadFileModal"));
            modal.show();
        });
    }

    // Upload Folder dari floating button
    const mobileOpenUploadFolderModal = document.getElementById("mobileOpenUploadFolderModal");
    if (mobileOpenUploadFolderModal) {
        mobileOpenUploadFolderModal.addEventListener("click", function (e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById("uploadFolderModal"));
            modal.show();
        });
    }
});
            document.addEventListener('DOMContentLoaded', function() {
                const $ = (s) => document.querySelector(s);
                const $$ = (s) => document.querySelectorAll(s);

                function syncProfilePhotosOnLoad() {
                    // Ambil foto dari desktop sebagai referensi
                    const desktopPhoto = $('#profilePreviewBtn, #profilePreviewDropdown');
                    const mobilePhoto = $('#mobileProfilePhoto');
                    const modalPhoto = $('#modalProfilePhoto');

                    if (desktopPhoto && mobilePhoto && desktopPhoto.src) {
                        // Sync mobile dengan desktop
                        mobilePhoto.src = desktopPhoto.src;
                        if (modalPhoto) modalPhoto.src = desktopPhoto.src;
                    }
                }

                // Jalankan sync saat load
                syncProfilePhotosOnLoad();
                // ===================================================================
                // 1. DESKTOP ONLY: Dropdown, Filter, Profile Dropdown
                // ===================================================================
                function closeAllDesktop() {
                    $$('.desktop-only .dropdown-menu-custom, .desktop-only #filterPanel, .desktop-only #profileDropdown')
                        .forEach(el => el && (el.style.display = 'none'));
                    $$('.desktop-only .dropdown-toggle-custom').forEach(btn => btn.classList.remove('active'));
                }

                // Dropdown biasa (sort, filter, dll)
                $$('.desktop-only .dropdown-toggle-custom').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        const menu = $(btn.dataset.target);
                        const isOpen = menu?.style.display === 'block';
                        closeAllDesktop();
                        if (!isOpen && menu) {
                            menu.style.display = 'block';
                            btn.classList.add('active');
                        }
                    });
                });
                // Dropdown biasa (sort, filter, dll)
                $$('.desktop-only .dropdown-toggle-custom').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        const menu = $(btn.dataset.target);
                        const isOpen = menu?.style.display === 'block';
                        closeAllDesktop();
                        if (!isOpen && menu) {
                            menu.style.display = 'block';
                            btn.classList.add('active');
                        }
                    });
                });

                // Profile dropdown desktop
                $('.desktop-only #profileBtn')?.addEventListener('click', e => {
                    e.stopPropagation();
                    const dd = $('.desktop-only #profileDropdown');
                    if (dd) dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
                });
                // Profile dropdown desktop
                $('.desktop-only #profileBtn')?.addEventListener('click', e => {
                    e.stopPropagation();
                    const dd = $('.desktop-only #profileDropdown');
                    if (dd) dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
                });

                // Filter panel desktop
                $('.desktop-only .filter-toggle')?.addEventListener('click', e => {
                    e.stopPropagation();
                    const panel = $('.desktop-only #filterPanel');
                    if (panel) panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
                });
                // Filter panel desktop
                $('.desktop-only .filter-toggle')?.addEventListener('click', e => {
                    e.stopPropagation();
                    const panel = $('.desktop-only #filterPanel');
                    if (panel) panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
                });

                // Klik di luar → hanya nutup desktop
                document.addEventListener('click', e => {
                    if (!e.target.closest('.desktop-only')) closeAllDesktop();
                });
                // Klik di luar → hanya nutup desktop
                document.addEventListener('click', e => {
                    if (!e.target.closest('.desktop-only')) closeAllDesktop();
                });

                // ===================================================================
                // 2. MOBILE & DESKTOP: Upload Foto (satu fungsi untuk semua!)
                // ===================================================================
                function updateAllProfilePhotos(src) {
                    $$('#mobileProfilePhoto, #modalProfilePhoto, #profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                        .forEach(img => img && (img.src = src));
                }
                // ===================================================================
                // 2. MOBILE & DESKTOP: Upload Foto (satu fungsi untuk semua!)
                // ===================================================================
                function updateAllProfilePhotos(src) {
                    $$('#mobileProfilePhoto, #modalProfilePhoto, #profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                        .forEach(img => img && (img.src = src));
                }

                // Upload dari mobile
                $('.mobile-only #uploadPhotoBtn')?.addEventListener('click', () => $('.mobile-only #photoInput')
                ?.click());
                $('.mobile-only #photoInput')?.addEventListener('change', function() {
                    if (this.files?.[0]) {
                        const reader = new FileReader();
                        reader.onload = e => updateAllProfilePhotos(e.target.result);
                        reader.readAsDataURL(this.files[0]);
                    }
                });
                // Upload dari mobile
                $('.mobile-only #uploadPhotoBtn')?.addEventListener('click', () => $('.mobile-only #photoInput')
                ?.click());
                $('.mobile-only #photoInput')?.addEventListener('change', function() {
                    if (this.files?.[0]) {
                        const reader = new FileReader();
                        reader.onload = e => updateAllProfilePhotos(e.target.result);
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // Upload dari desktop
                $('.desktop-only #uploadPhotoBtn')?.addEventListener('click', () => $('.desktop-only #photoFileInput')
                    ?.click());
                $('.desktop-only #photoFileInput')?.addEventListener('change', function() {
                    if (this.files?.[0]) {
                        const reader = new FileReader();
                        reader.onload = e => updateAllProfilePhotos(e.target.result);
                        reader.readAsDataURL(this.files[0]);
                    }
                });
                // Upload dari desktop
                $('.desktop-only #uploadPhotoBtn')?.addEventListener('click', () => $('.desktop-only #photoFileInput')
                    ?.click());
                $('.desktop-only #photoFileInput')?.addEventListener('change', function() {
                    if (this.files?.[0]) {
                        const reader = new FileReader();
                        reader.onload = e => updateAllProfilePhotos(e.target.result);
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // ===================================================================
                // 3. DELETE FOTO (mobile + desktop)
                // ===================================================================
                $$('.mobile-only #deletePhotoBtn, .desktop-only #deletePhotoBtn').forEach(btn => {
                    btn?.addEventListener('click', async () => {
                        if (!confirm('Hapus foto profil?')) return;
                // ===================================================================
                // 3. DELETE FOTO (mobile + desktop)
                // ===================================================================
                $$('.mobile-only #deletePhotoBtn, .desktop-only #deletePhotoBtn').forEach(btn => {
                    btn?.addEventListener('click', async () => {
                        if (!confirm('Hapus foto profil?')) return;

                        try {
                            const {
                                data
                            } = await axios.post('{{ route('profile.delete.photo') }}');
                            const url = data.photo_url + '?t=' + Date.now();
                            updateAllProfilePhotos(url);
                            alert('Foto dihapus!');
                        } catch (err) {
                            alert('Gagal hapus foto');
                        }
                    });
                });
                        try {
                            const {
                                data
                            } = await axios.post('{{ route('profile.delete.photo') }}');
                            const url = data.photo_url + '?t=' + Date.now();
                            updateAllProfilePhotos(url);
                            alert('Foto dihapus!');
                        } catch (err) {
                            alert('Gagal hapus foto');
                        }
                    });
                });

                // Konfirmasi delete desktop
                $('.desktop-only #confirmDeleteBtn')?.addEventListener('click', async () => {
                    try {
                        const {
                            data
                        } = await axios.post('{{ route('profile.delete.photo') }}');
                        updateAllProfilePhotos(data.photo_url + '?t=' + Date.now());
                        bootstrap.Modal.getInstance($('.desktop-only #deleteConfirmationModal'))?.hide();
                        alert('Foto dihapus!');
                    } catch (err) {
                        alert('Gagal');
                    }
                });
                // Konfirmasi delete desktop
                $('.desktop-only #confirmDeleteBtn')?.addEventListener('click', async () => {
                    try {
                        const {
                            data
                        } = await axios.post('{{ route('profile.delete.photo') }}');
                        updateAllProfilePhotos(data.photo_url + '?t=' + Date.now());
                        bootstrap.Modal.getInstance($('.desktop-only #deleteConfirmationModal'))?.hide();
                        alert('Foto dihapus!');
                    } catch (err) {
                        alert('Gagal');
                    }
                });

                // ===================================================================
                // 4. UPDATE PROFILE (mobile + desktop)
                // ===================================================================
                $$('#profileUpdateForm').forEach(form => {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const btn = this.querySelector('button[type="submit"]');
                        const orig = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
                // ===================================================================
                // 4. UPDATE PROFILE (mobile + desktop)
                // ===================================================================
                $$('#profileUpdateForm').forEach(form => {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const btn = this.querySelector('button[type="submit"]');
                        const orig = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

                        try {
                            const {
                                data
                            } = await axios.post(this.action, new FormData(this));
                            const url = (data.photo_url || data.photo_path) + '?t=' + Date.now();
                            updateAllProfilePhotos(url);
                            $$('.mobile-only #mobileProfileName, .desktop-only .profile-fullname')
                                .forEach(el => el.textContent = data.fullname || data.name);
                        try {
                            const {
                                data
                            } = await axios.post(this.action, new FormData(this));
                            const url = (data.photo_url || data.photo_path) + '?t=' + Date.now();
                            updateAllProfilePhotos(url);
                            $$('.mobile-only #mobileProfileName, .desktop-only .profile-fullname')
                                .forEach(el => el.textContent = data.fullname || data.name);

                            // Tutup modal sesuai versi
                            const modalId = this.closest('.modal')?.id;
                            if (modalId) bootstrap.Modal.getInstance(document.getElementById(
                                modalId))?.hide();
                            // Tutup modal sesuai versi
                            const modalId = this.closest('.modal')?.id;
                            if (modalId) bootstrap.Modal.getInstance(document.getElementById(
                                modalId))?.hide();

                            alert('Profil diperbarui!');
                        } catch (err) {
                            alert(err.response?.data?.message || 'Gagal update');
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = orig;
                        }
                    });
                });
                            alert('Profil diperbarui!');
                        } catch (err) {
                            alert(err.response?.data?.message || 'Gagal update');
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = orig;
                        }
                    });
                });

                // ===================================================================
                // 5. CHANGE PASSWORD (mobile + desktop)
                // ===================================================================
                $$('#changePasswordForm').forEach(form => {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const btn = this.querySelector('button[type="submit"]') || $(
                            '.desktop-only #submitChangePassword');
                        const orig = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

                        try {
                            await axios.post('{{ route('password.update') }}', new FormData(this));
                            alert('Password diubah! Keluar otomatis...');
                            setTimeout(() => location.href = '/signin', 1500);
                        } catch (err) {
                            alert(err.response?.data?.message || 'Gagal ubah password');
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = orig;
                        }
                    });
                });
                        try {
                            await axios.post('{{ route('password.update') }}', new FormData(this));
                            alert('Password diubah! Keluar otomatis...');
                            setTimeout(() => location.href = '/signin', 1500);
                        } catch (err) {
                            alert(err.response?.data?.message || 'Gagal ubah password');
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = orig;
                        }
                    });
                });

                // ===================================================================
                // 6. TOGGLE EYE PASSWORD
                // ===================================================================
                $$('.toggle-password').forEach(icon => {
                    icon.onclick = function() {
                        const input = document.querySelector(`input[name="${this.dataset.target}"]`);
                        if (!input) return;
                        if (input.type === 'password') {
                            input.type = 'text';
                            this.classList.replace('ph-eye-slash', 'ph-eye');
                        } else {
                            input.type = 'password';
                            this.classList.replace('ph-eye', 'ph-eye-slash');
                        }
                    };
                });
            });
        </script>
                // ===================================================================
                // 6. TOGGLE EYE PASSWORD
                // ===================================================================
                $$('.toggle-password').forEach(icon => {
                    icon.onclick = function() {
                        const input = document.querySelector(`input[name="${this.dataset.target}"]`);
                        if (!input) return;
                        if (input.type === 'password') {
                            input.type = 'text';
                            this.classList.replace('ph-eye-slash', 'ph-eye');
                        } else {
                            input.type = 'password';
                            this.classList.replace('ph-eye', 'ph-eye-slash');
                        }
                    };
                });
            });
        </script>

    </body>

</html>
