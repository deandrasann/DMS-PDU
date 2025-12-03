<div id="sidebar"
    class="d-flex vh-100 flex-column flex-shrink-0 p-3 bg-light shadow mb-2 rounded-4 position-fixed sidebar-full"
    style="width: 280px; transition: width 0.3s;">
    <div class="d-flex align-items-center justify-content-between mb-3 position-relative">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/logo v1.png') }}" class="d-flex align-items-center ms-2 p-1" alt="Logo Kecil"
                id="sidebarLogoSmall" style="width:36px;">

            <!-- Logo besar (muncul saat expanded) -->
            <img src="{{ asset('img/logo v2.png') }}" alt="Logo Besar" id="sidebarLogoBig" style="width:120px;">
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
            <a href="{{ route('recommended') }}"
                class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('recommended') ? 'active' : '' }}">
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
                class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('myspace') || request()->routeIs('myspace.subfolder') ? 'active' : '' }}">
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

                            <!-- Tombol Add Label (akan berubah jadi input) -->
                            <div id="addLabelContainer">
                                <button type="button"
                                    class="btn btn-outline-primary rounded-3 d-flex align-items-center"
                                    id="addLabelBtn">
                                    <i class="ph ph-plus me-2"></i> Add Label
                                </button>
                                <button type="button"
                                    class="btn btn-outline-primary rounded-3 d-flex align-items-center"
                                    id="addLabelBtn">
                                    <i class="ph ph-plus me-2"></i> Delete Label
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

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmationModal" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background-color: #fff; max-width: 500px;">

            <!-- Close Button (Kanan Atas) -->
            <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal"
                aria-label="Close" style=" font-size: 1.1rem; opacity: 0.7;">
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
                        class="btn btn-danger rounded-4 px-4 py-2 text-white position-relative"
                        style="min-width: 100px; font-size:14px; background-color: #dc3545; border: none;">
                        <span class="btn-text">Log out</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Logging out...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="globalLogoutLoader" class="position-fixed top-0 start-0 w-100 h-100 d-none"
     style="background: rgba(255, 255, 255, 0.94); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 99999;">
    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-orange">
        <div class="spinner-border mb-4" style="width: 3.5rem; height: 3.5rem;" role="status">
            <span class="visually-hidden">Logging out...</span>
        </div>
        <h5 class="fw-semibold">Signing you out...</h5>
        <small class="text-muted">Please wait a moment</small>
    </div>
</div>

<!-- Edit File Modal -->
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

{{-- MODAL RENAME FOLDER --}}
<div class="modal fade" id="renameFolderModal" tabindex="-1" aria-labelledby="renameFolderModalLabel" aria-hidden="true" data-bs-backdrop="false">
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
                        <input type="text" class="form-control rounded-3 border-dark-subtle"
                               id="newFolderName" name="new_name" required
                               placeholder="Enter new folder name">
                        <div class="form-text text-muted">
                            Folder name cannot contain special characters.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-blue rounded-3 px-4" id="confirmRenameFolder">Rename</button>
            </div>
        </div>
    </div>
</div>

<script>
//     // ==================== GLOBAL VARIABLES ====================
//     let selectedLabelIds = []; // Untuk upload modal
//     let allLabels = []; // Semua labels dari API
//     let editSelectedLabelIds = []; // Untuk edit modal

//     // 🎨 Warna background untuk label
//     const labelColors = [
//         "FDDCD9", "EBE0D9", "FDE9DD", "EFEAFF", "FCF9DE",
//         "E4F3FE", "FCE7ED", "E6E5E3", "EEFEF1", "F0EFED"
//     ];

//     // 🎨 Map background → text color
//     const colorMap = {
//         "FDDCD9": "#CB564A",
//         "EBE0D9": "#763E1A",
//         "FDE9DD": "#C2825D",
//         "EFEAFF": "#7762BB",
//         "FCF9DE": "#BDB470",
//         "E4F3FE": "#5F92B6",
//         "FCE7ED": "#CA8499",
//         "E6E5E3": "#989797",
//         "EEFEF1": "#8ABB93",
//         "F0EFED": "#729D9C"
//     };

//     // ==================== GLOBAL FUNCTIONS ====================

//     // Fungsi untuk mendapatkan token
//     function getToken() {
//         return "{{ session('token') }}";
//     }

//     // Fungsi untuk menampilkan pesan feedback
//     function showMessage(message, type, containerId = null) {
//         let messageDiv;

//         if (containerId) {
//             messageDiv = document.getElementById(containerId);
//         } else {
//             // Default container untuk modal yang sedang aktif
//             const activeModal = document.querySelector('.modal.show');
//             if (activeModal) {
//                 messageDiv = activeModal.querySelector('.alert');
//                 if (!messageDiv) {
//                     // Buat elemen alert jika belum ada
//                     messageDiv = document.createElement('div');
//                     messageDiv.className = 'alert d-none';
//                     activeModal.querySelector('.modal-body').prepend(messageDiv);
//                 }
//             } else {
//                 // Fallback ke alert biasa jika tidak ada modal aktif
//                 alert(message);
//                 return;
//             }
//         }

//         if (!messageDiv) {
//             console.error('Message container not found');
//             return;
//         }

//         messageDiv.textContent = message;
//         messageDiv.className = `alert alert-${type} mt-3`;
//         messageDiv.classList.remove("d-none");

//         // Auto-hide untuk pesan sukses
//         if (type === "success") {
//             setTimeout(() => {
//                 messageDiv.classList.add("d-none");
//             }, 5000);
//         }

//         // Scroll ke pesan
//         messageDiv.scrollIntoView({
//             behavior: 'smooth',
//             block: 'nearest'
//         });
//     }

//     // 🟢 Ambil semua label dari API
//     async function loadLabels() {
//         const token = getToken();
//         if (!token) {
//             console.error('No token available');
//             return;
//         }

//         try {
//             const res = await fetch("https://pdu-dms.my.id/api/labels", {
//                 headers: {
//                     "Authorization": "Bearer " + token
//                 }
//             });

//             if (!res.ok) {
//                 if (res.status === 401) {
//                     window.location.href = "{{ route('signin') }}";
//                     return;
//                 }
//                 throw new Error(`HTTP ${res.status}`);
//             }

//             const data = await res.json();
//             allLabels = data.data || [];

//             // Render labels untuk upload modal
//             renderExistingLabels();
//             renderSelectedLabels();

//             // Render labels untuk edit modal
//             if (window.renderEditExistingLabels) {
//                 window.renderEditExistingLabels();
//             }

//             console.log('Labels loaded successfully:', allLabels.length);

//         } catch (err) {
//             console.error("Gagal memuat label:", err);
//         }
//     }

//     // Render daftar label yang tersedia (UPLOAD MODAL)
//     // Render daftar label yang tersedia (UPLOAD MODAL) dengan delete button
// // Render daftar label yang tersedia (UPLOAD MODAL) dengan delete button di dalam badge
// function renderExistingLabels() {
//     const existingLabelsContainer = document.getElementById("existingLabels");
//     if (!existingLabelsContainer) return;

//     existingLabelsContainer.innerHTML = "";

//     allLabels.forEach(label => {
//         const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
//         const textColor = colorMap[label.color] || "#333";

//         // Buat container untuk label + delete button dalam satu badge
//         const labelContainer = document.createElement("div");
//         labelContainer.classList.add("d-inline-flex", "align-items-center", "rounded-pill", "px-3", "py-2", "small", "me-2", "mb-2");
//         labelContainer.style.backgroundColor = bgColor;
//         labelContainer.style.color = textColor;
//         labelContainer.style.border = `1px solid ${textColor}22`;
//         labelContainer.style.cursor = "pointer";
//         labelContainer.style.transition = "all 0.2s ease";

//         // Hover effects untuk seluruh container
//         labelContainer.addEventListener("mouseenter", () => {
//             labelContainer.style.opacity = "0.9";
//             labelContainer.style.transform = "scale(1.02)";
//         });
//         labelContainer.addEventListener("mouseleave", () => {
//             labelContainer.style.opacity = "1";
//             labelContainer.style.transform = "scale(1)";
//         });

//         // Text label
//         const labelText = document.createElement("span");
//         labelText.textContent = label.name;
//         labelText.classList.add("me-1");

//         // Delete button (dropdown) - lebih kecil dan compact
//         const deleteDropdown = document.createElement("div");
//         deleteDropdown.classList.add("dropdown");

//         deleteDropdown.innerHTML = `
//             <button class="btn btn-link p-0 shadow-none"
//                     data-bs-toggle="dropdown"
//                     data-bs-display="static"
//                     style="font-size: 0.6rem; color: ${textColor}; line-height: 1;">
//                 <i class="ph ph-dots-three-vertical"></i>
//             </button>
//             <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
//                 <li>
//                     <a class="dropdown-item d-flex align-items-center gap-2 text-danger delete-existing-label-btn"
//                        href="#"
//                        data-label-id="${label.id}"
//                        data-label-name="${label.name}">
//                         <i class="ph ph-trash fs-5"></i> Delete Label
//                     </a>
//                 </li>
//             </ul>
//         `;

//         // Click handler untuk memilih label (klik area text)
//         labelText.addEventListener("click", (e) => {
//             e.stopPropagation();
//             if (!selectedLabelIds.includes(label.id)) {
//                 selectedLabelIds.push(label.id);
//                 renderSelectedLabels();
//             }
//         });

//         // Click handler untuk container (fallback)
//         labelContainer.addEventListener("click", (e) => {
//             if (!e.target.closest('.dropdown')) {
//                 if (!selectedLabelIds.includes(label.id)) {
//                     selectedLabelIds.push(label.id);
//                     renderSelectedLabels();
//                 }
//             }
//         });

//         labelContainer.appendChild(labelText);
//         labelContainer.appendChild(deleteDropdown);
//         existingLabelsContainer.appendChild(labelContainer);
//     });
// }

//     // Render label yang sudah dipilih (UPLOAD MODAL)
//     function renderSelectedLabels() {
//         const selectedLabelsContainer = document.getElementById("selectedLabels");
//         if (!selectedLabelsContainer) return;

//         selectedLabelsContainer.innerHTML = "";

//         selectedLabelIds.forEach(labelId => {
//             const label = allLabels.find(l => l.id === labelId);
//             if (label) {
//                 const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
//                 const textColor = colorMap[label.color] || "#333";

//                 const labelElement = document.createElement("div");
//                 labelElement.classList.add("d-flex", "align-items-center", "gap-2", "rounded-pill", "px-3",
//                     "py-2", "small", "me-2", "mb-2");
//                 labelElement.style.backgroundColor = bgColor;
//                 labelElement.style.color = textColor;
//                 labelElement.style.border = `1px solid ${textColor}22`;

//                 const labelText = document.createElement("span");
//                 labelText.textContent = label.name;

//                 const removeBtn = document.createElement("button");
//                 removeBtn.type = "button";
//                 removeBtn.classList.add("btn", "btn-sm", "p-0", "shadow-none");
//                 removeBtn.innerHTML = '<i class="ph ph-x shadow-none" style="font-size: 12px;"></i>';
//                 removeBtn.style.color = textColor;
//                 removeBtn.style.border = "none";
//                 removeBtn.style.background = "none";
//                 removeBtn.style.cursor = "pointer";

//                 removeBtn.addEventListener("click", () => {
//                     selectedLabelIds = selectedLabelIds.filter(id => id !== labelId);
//                     renderSelectedLabels();
//                 });

//                 labelElement.appendChild(labelText);
//                 labelElement.appendChild(removeBtn);
//                 selectedLabelsContainer.appendChild(labelElement);
//             }
//         });
//     }

//     // ==================== SIDEBAR TOGGLE FUNCTIONALITY ====================
//     document.addEventListener("DOMContentLoaded", () => {
//         const sidebar = document.getElementById('sidebar');
//         const toggleBtn = document.getElementById('toggleBtn');
//         const contentArea = document.querySelector('.sidebar-collapse-content');
//         const texts = document.querySelectorAll('.sidebar-text');

//         toggleBtn.addEventListener('click', function() {
//             sidebar.classList.toggle('collapsed');
//             contentArea.classList.toggle('collapsed');

//             if (sidebar.classList.contains('collapsed')) {
//                 texts.forEach(t => {
//                     t.classList.add('opacity-0');
//                     setTimeout(() => t.classList.add('d-none'), 200);
//                 });
//             } else {
//                 texts.forEach(t => {
//                     t.classList.remove('d-none');
//                     setTimeout(() => t.classList.remove('opacity-0'), 50);
//                 });
//             }
//         });
//     });

//     // ==================== MODAL OPENERS ====================
//     document.getElementById("openUploadFolderModal").addEventListener("click", function(e) {
//         e.preventDefault();
//         const modal = new bootstrap.Modal(document.getElementById("uploadFolderModal"));

//         // Reset form
//         document.getElementById("createFolderForm").reset();
//         document.getElementById("folderMessage").classList.add("d-none");

//         modal.show();
//     });

//     document.getElementById("openUploadModal").addEventListener("click", function(e) {
//         e.preventDefault();
//         const modal = new bootstrap.Modal(document.getElementById("uploadFileModal"));

//         // Reset form dan labels
//         document.getElementById("uploadForm").reset();
//         document.getElementById("fileName").textContent = "";
//         document.getElementById("uploadMessage").classList.add("d-none");

//         // Reset selected labels
//         selectedLabelIds = [];
//         const selectedLabelsContainer = document.getElementById("selectedLabels");
//         if (selectedLabelsContainer) {
//             selectedLabelsContainer.innerHTML = "";
//         }

//         modal.show();
//     });

    // ==================== LOGOUT FUNCTIONALITY ====================
    document.addEventListener("DOMContentLoaded", function() {
        const logoutLink = document.getElementById('logoutLink');
        const logoutForm = document.getElementById('logout-form');
        const logoutBtn = document.getElementById('confirmLogoutBtn');
        const loader = document.getElementById('globalLogoutLoader');
        const btnText = logoutBtn.querySelector('.btn-text');
        const btnLoading = logoutBtn.querySelector('.btn-loading');

<<<<<<< HEAD
//         // Buka modal saat klik "Log Out"
//         logoutLink.addEventListener('click', function(e) {
//             e.preventDefault();
//             const modal = new bootstrap.Modal(document.getElementById('logoutConfirmationModal'));
//             modal.show();
//         });
=======
        // Buka modal saat klik "Log Out"
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('logoutConfirmationModal'));
            modal.show();
        });
>>>>>>> 87ec885 (Revert "Merge branch 'master' into aul-fe")

        if (logoutBtn && loader) {
            logoutBtn.addEventListener('click', function () {
                // Disable tombol
                logoutBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');

                // Tampilkan full screen loading + blur
                loader.classList.remove('d-none');

                // Submit form logout (pastikan ada form logout dengan id atau name "logout-form")
                // Biasanya Laravel punya form logout seperti ini:
                const logoutForm = document.querySelector('form[action*="logout"]') ||
                                  document.getElementById('logout-form');

                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    // Fallback: langsung redirect ke route logout
                    window.location.href = "{{ route('logout') }}";
                }
            });
        }
    });

//     // ==================== LABEL MANAGEMENT FUNCTIONALITY ====================
//     document.addEventListener("DOMContentLoaded", async () => {
//         const token = getToken();

//         if (!token) {
//             console.error('No token available in session');
//             window.location.href = "{{ route('signin') }}";
//             return;
//         }

//         const addLabelBtn = document.getElementById("addLabelBtn");
//         const newLabelContainer = document.getElementById("newLabelContainer");
//         const newLabelInput = document.getElementById("newLabelInput");
//         const saveNewLabelBtn = document.getElementById("saveNewLabelBtn");
//         const cancelNewLabelBtn = document.getElementById("cancelNewLabelBtn");

//         // 🔘 Klik tombol Add → ganti dengan input
//         addLabelBtn.addEventListener("click", () => {
//             addLabelBtn.classList.add("d-none");
//             newLabelContainer.classList.remove("d-none");
//             newLabelInput.focus();
//         });

//         // 💾 Simpan label baru
//         saveNewLabelBtn.addEventListener("click", async () => {
//             const name = newLabelInput.value.trim();
//             if (!name) {
//                 showMessage("Label name cannot be empty", "danger", "uploadMessage");
//                 return;
//             }

//             const color = labelColors[Math.floor(Math.random() * labelColors.length)];

//             try {
//                 const res = await fetch("https://pdu-dms.my.id/api/create-label", {
//                     method: "POST",
//                     headers: {
//                         "Content-Type": "application/json",
//                         "Authorization": "Bearer " + token
//                     },
//                     body: JSON.stringify({
//                         name,
//                         color
//                     })
//                 });

//                 if (!res.ok) {
//                     if (res.status === 401) {
//                         window.location.href = "{{ route('signin') }}";
//                         return;
//                     }
//                     throw new Error(`HTTP ${res.status}`);
//                 }

//                 const result = await res.json();

//                 if (result.success === false) {
//                     throw new Error(result.message || "Failed to create label");
//                 }

//                 // Refresh labels
//                 await loadLabels();

//                 // Otomatis pilih label yang baru dibuat
//                 if (result.data && result.data.id) {
//                     selectedLabelIds.push(result.data.id);
//                     renderSelectedLabels();
//                 }

//                 // Kembalikan ke tombol Add
//                 resetAddLabelForm();

//                 showMessage("Label created successfully!", "success", "uploadMessage");

//             } catch (err) {
//                 showMessage("Failed to create label: " + err.message, "danger",
//                 "uploadMessage");
//             }
//         });

//         // ❌ Cancel pembuatan label baru
//         cancelNewLabelBtn.addEventListener("click", () => {
//             resetAddLabelForm();
//         });

//         // Fungsi untuk reset form add label
//         function resetAddLabelForm() {
//             newLabelInput.value = "";
//             newLabelContainer.classList.add("d-none");
//             addLabelBtn.classList.remove("d-none");
//         }

//         // Submit dengan Enter di input label baru
//         newLabelInput.addEventListener("keypress", (e) => {
//             if (e.key === "Enter") {
//                 e.preventDefault();
//                 saveNewLabelBtn.click();
//             }
//         });

//         // Jalankan load awal
//         loadLabels();
//     });

//     // ==================== UPLOAD FILE FUNCTIONALITY ====================
//     const uploadArea = document.getElementById("uploadArea");
//     const fileInput = document.getElementById("fileInput");
//     const fileNameDisplay = document.getElementById("fileName");

//     // Klik area upload → buka file picker
//     uploadArea.addEventListener("click", () => fileInput.click());

//     // Preview nama file
//     fileInput.addEventListener("change", () => {
//         if (fileInput.files.length > 0) {
//             const file = fileInput.files[0];
//             fileNameDisplay.textContent = file.name;

//             // Auto-fill title dengan nama file tanpa extension
//             const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
//             document.getElementById("title").value = fileNameWithoutExt;
//         } else {
//             fileNameDisplay.textContent = "";
//             document.getElementById("title").value = "";
//         }
//     });

//     // Efek drag & drop
//     uploadArea.addEventListener("dragover", (e) => {
//         e.preventDefault();
//         uploadArea.style.borderColor = "#0d6efd";
//         uploadArea.style.backgroundColor = "rgba(13, 110, 253, 0.05)";
//     });

//     uploadArea.addEventListener("dragleave", () => {
//         uploadArea.style.borderColor = "#dee2e6";
//         uploadArea.style.backgroundColor = "transparent";
//     });

//     uploadArea.addEventListener("drop", (e) => {
//         e.preventDefault();
//         uploadArea.style.borderColor = "#dee2e6";
//         uploadArea.style.backgroundColor = "transparent";

//         fileInput.files = e.dataTransfer.files;

//         if (fileInput.files.length > 0) {
//             const file = fileInput.files[0];
//             fileNameDisplay.textContent = file.name;

//             // Auto-fill title dengan nama file tanpa extension
//             const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
//             document.getElementById("title").value = fileNameWithoutExt;
//         }
//     });

//     // Submit form upload file
//     document.getElementById("uploadForm").addEventListener("submit", async function(e) {
//         e.preventDefault();

//         const token = getToken();
//         if (!token) {
//             showMessage("Session expired. Please login again.", "danger", "uploadMessage");
//             window.location.href = "{{ route('signin') }}";
//             return;
//         }

//         const file = fileInput.files[0];
//         const parentId = getParentIdFromUrl();

//         if (!file) {
//             showMessage("Please select a file to upload", "danger", "uploadMessage");
//             return;
//         }

//         try {
//             const formData = new FormData();
//             const title = document.getElementById("title").value.trim();

//             // Jika title diisi, gunakan sebagai custom filename
//             if (title) {
//                 const originalExtension = file.name.split('.').pop();
//                 const customFileName = `${title}.${originalExtension}`;
//                 formData.append("files[]", file, customFileName);
//                 formData.append("relative_paths[]", customFileName);
//             } else {
//                 formData.append("files[]", file);
//                 formData.append("relative_paths[]", file.name);
//             }

//             if (parentId) formData.append("parent_id", parentId);

//             // Tambahkan labels jika ada
//             if (selectedLabelIds.length > 0) {
//                 selectedLabelIds.forEach(labelId => {
//                     formData.append("labels[]", labelId);
//                 });
//             }

//             const submitBtn = this.querySelector('button[type="submit"]');
//             const originalBtnText = submitBtn.innerHTML;

//             // Tampilkan loading state
//             submitBtn.disabled = true;
//             submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

//             const res = await fetch("https://pdu-dms.my.id/api/upload-files", {
//                 method: "POST",
//                 headers: {
//                     "Authorization": "Bearer " + token
//                 },
//                 body: formData
//             });

//             if (!res.ok) {
//                 if (res.status === 401) {
//                     showMessage("Session expired. Please login again.", "danger", "uploadMessage");
//                     window.location.href = "{{ route('signin') }}";
//                     return;
//                 }
//                 throw new Error(`HTTP ${res.status}`);
//             }

//             const result = await res.json();
//             if (!res.ok) throw new Error(result.message || "Failed to upload file");

//             showMessage("File uploaded successfully!", "success", "uploadMessage");

//             setTimeout(() => {
//                 bootstrap.Modal.getInstance(document.getElementById("uploadFileModal")).hide();
//                 location.reload();
//             }, 1500);

//         } catch (err) {
//             showMessage("Failed to upload: " + err.message, "danger", "uploadMessage");

//             // Reset button state
//             const submitBtn = this.querySelector('button[type="submit"]');
//             submitBtn.disabled = false;
//             submitBtn.innerHTML = 'Upload';
//         }
//     });

//     // ==================== CREATE FOLDER FUNCTIONALITY ====================

//     // Fungsi untuk mendapatkan parent_id dari URL
//     function getParentIdFromUrl() {
//         const currentUrl = window.location.href;
//         const urlParts = currentUrl.split('/');

//         const myspaceIndex = urlParts.indexOf('myspace');
//         if (myspaceIndex !== -1 && urlParts.length > myspaceIndex + 1) {
//             const potentialId = urlParts[myspaceIndex + 1];
//             if (potentialId && !isNaN(potentialId) && potentialId.trim() !== '') {
//                 return parseInt(potentialId);
//             }
//         }

//         return null;
//     }

//     // Fungsi untuk membuat folder baru
//     document.getElementById("createFolderBtn").addEventListener("click", async function() {
//         const token = getToken();
//         if (!token) {
//             showMessage("Session expired. Please login again.", "danger", "folderMessage");
//             window.location.href = "{{ route('signin') }}";
//             return;
//         }

//         const folderName = document.getElementById("folderName").value.trim();
//         const messageDiv = document.getElementById("folderMessage");

//         if (!folderName) {
//             showMessage("Please enter a folder name", "danger", "folderMessage");
//             return;
//         }

//         const parentId = getParentIdFromUrl();

//         const folderData = {
//             name: folderName
//         };

//         if (parentId) {
//             folderData.parent_id = parentId;
//         }

//         try {
//             this.disabled = true;
//             this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

//             const res = await fetch("https://pdu-dms.my.id/api/create-folder", {
//                 method: "POST",
//                 headers: {
//                     "Content-Type": "application/json",
//                     "Authorization": "Bearer " + token
//                 },
//                 body: JSON.stringify(folderData)
//             });

//             if (!res.ok) {
//                 if (res.status === 401) {
//                     showMessage("Session expired. Please login again.", "danger", "folderMessage");
//                     window.location.href = "{{ route('signin') }}";
//                     return;
//                 }
//                 throw new Error(`HTTP ${res.status}`);
//             }

//             const result = await res.json();

//             if (res.ok) {
//                 showMessage("Folder created successfully!", "success", "folderMessage");

//                 setTimeout(() => {
//                     document.getElementById("createFolderForm").reset();
//                     bootstrap.Modal.getInstance(document.getElementById("uploadFolderModal"))
//                 .hide();
//                     location.reload();
//                 }, 1500);
//             } else {
//                 throw new Error(result.message || "Failed to create folder");
//             }
//         } catch (err) {
//             console.error("Error creating folder:", err);
//             showMessage("Failed to create folder: " + err.message, "danger", "folderMessage");
//         } finally {
//             this.disabled = false;
//             this.innerHTML = "Create Folder";
//         }
//     });

//     // Submit form dengan tombol Enter
//     document.getElementById("folderName").addEventListener("keypress", function(e) {
//         if (e.key === "Enter") {
//             e.preventDefault();
//             document.getElementById("createFolderBtn").click();
//         }
//     });

//     // ==================== EDIT FILE FUNCTIONALITY ====================

//     // Global function untuk membuka modal edit dari mana saja
//     window.openEditModal = async function(fileId, fileName, fileLabels) {
//         // Set data file ke form
//         document.getElementById('editFileId').value = fileId;
//         document.getElementById('editTitle').value = fileName.replace(/\.[^/.]+$/, ""); // Remove extension
//         document.getElementById('editFileNameDisplay').textContent = fileName;

//         // Set file info
//         const fileExtension = fileName.split('.').pop().toUpperCase();
//         document.getElementById('editFileInfo').textContent = `${fileExtension} File`;

//         // Set labels yang sudah ada
//         editSelectedLabelIds = fileLabels.map(label => label.id);

//         // Pastikan labels sudah di-load
//         if (allLabels.length === 0) {
//             await loadLabels();
//         }

//         renderEditSelectedLabels();

//         // Buka modal
//         const modal = new bootstrap.Modal(document.getElementById('editFileModal'));
//         modal.show();
//     };

//     // Initialize edit functionality
//     function initializeEditFunctionality() {
//         // Event delegation untuk tombol edit file di seluruh aplikasi
//         document.addEventListener('click', function(e) {
//             if (e.target.closest('.edit-file-btn')) {
//                 e.preventDefault();
//                 const button = e.target.closest('.edit-file-btn');
//                 const fileId = button.getAttribute('data-id');
//                 const fileName = button.getAttribute('data-name');
//                 const fileLabels = JSON.parse(button.getAttribute('data-labels') || '[]');

//                 window.openEditModal(fileId, fileName, fileLabels);
//             }
//         });

//         initializeEditLabels();
//     }

//     function initializeEditLabels() {
//         const editExistingLabelsContainer = document.getElementById("editExistingLabels");
//         const editSelectedLabelsContainer = document.getElementById("editSelectedLabels");
//         const editAddLabelBtn = document.getElementById("editAddLabelBtn");
//         const editNewLabelContainer = document.getElementById("editNewLabelContainer");
//         const editNewLabelInput = document.getElementById("editNewLabelInput");
//         const editSaveNewLabelBtn = document.getElementById("editSaveNewLabelBtn");
//         const editCancelNewLabelBtn = document.getElementById("editCancelNewLabelBtn");

//         // Render daftar label yang tersedia untuk edit
//         // Render daftar label yang tersedia untuk edit dengan delete button
// // Render daftar label yang tersedia untuk edit dengan delete button di dalam badge
// function renderEditExistingLabels() {
//     const editExistingLabelsContainer = document.getElementById("editExistingLabels");
//     if (!editExistingLabelsContainer) return;

//     editExistingLabelsContainer.innerHTML = "";

//     if (!allLabels || allLabels.length === 0) {
//         editExistingLabelsContainer.innerHTML = `
//             <div class="text-muted small">No labels available. Create one first.</div>
//         `;
//         return;
//     }

//     allLabels.forEach(label => {
//         const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
//         const textColor = colorMap[label.color] || "#333";

//         // Buat container untuk label + delete button dalam satu badge
//         const labelContainer = document.createElement("div");
//         labelContainer.classList.add("d-inline-flex", "align-items-center", "rounded-pill", "px-3", "py-2", "small", "me-2", "mb-2");
//         labelContainer.style.backgroundColor = bgColor;
//         labelContainer.style.color = textColor;
//         labelContainer.style.border = `1px solid ${textColor}22`;
//         labelContainer.style.cursor = "pointer";
//         labelContainer.style.transition = "all 0.2s ease";

//         // Jika label sudah dipilih, beri style berbeda
//         if (editSelectedLabelIds.includes(label.id)) {
//             labelContainer.style.opacity = "0.7";
//             labelContainer.style.border = `2px solid ${textColor}`;
//         }

//         // Hover effects untuk seluruh container
//         labelContainer.addEventListener("mouseenter", () => {
//             labelContainer.style.opacity = "0.9";
//             labelContainer.style.transform = "scale(1.02)";
//         });
//         labelContainer.addEventListener("mouseleave", () => {
//             if (!editSelectedLabelIds.includes(label.id)) {
//                 labelContainer.style.opacity = "1";
//                 labelContainer.style.transform = "scale(1)";
//             } else {
//                 labelContainer.style.opacity = "0.7";
//                 labelContainer.style.transform = "scale(1)";
//             }
//         });

//         // Text label
//         const labelText = document.createElement("span");
//         labelText.textContent = label.name;
//         labelText.classList.add("me-1");

//         // Delete button (dropdown) - lebih kecil dan compact
//         const deleteDropdown = document.createElement("div");
//         deleteDropdown.classList.add("dropdown");

//         deleteDropdown.innerHTML = `
//             <button class="btn btn-link p-0 shadow-none"
//                     data-bs-toggle="dropdown"
//                     data-bs-display="static"
//                     style="font-size: 0.6rem; color: ${textColor}; line-height: 1;">
//                 <i class="ph ph-dots-three-vertical"></i>
//             </button>
//             <ul class="dropdown-menu shadow rounded-3 border-0 p-2">
//                 <li>
//                     <a class="dropdown-item d-flex align-items-center gap-2 text-danger delete-existing-label-btn"
//                        href="#"
//                        data-label-id="${label.id}"
//                        data-label-name="${label.name}">
//                         <i class="ph ph-trash fs-5"></i> Delete Label
//                     </a>
//                 </li>
//             </ul>
//         `;

//         // Click handler untuk memilih label (klik area text)
//         labelText.addEventListener("click", (e) => {
//             e.stopPropagation();
//             if (!editSelectedLabelIds.includes(label.id)) {
//                 editSelectedLabelIds.push(label.id);
//                 renderEditSelectedLabels();
//                 renderEditExistingLabels();
//             } else {
//                 editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== label.id);
//                 renderEditSelectedLabels();
//                 renderEditExistingLabels();
//             }
//         });

//         // Click handler untuk container (fallback)
//         labelContainer.addEventListener("click", (e) => {
//             if (!e.target.closest('.dropdown')) {
//                 if (!editSelectedLabelIds.includes(label.id)) {
//                     editSelectedLabelIds.push(label.id);
//                     renderEditSelectedLabels();
//                     renderEditExistingLabels();
//                 } else {
//                     editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== label.id);
//                     renderEditSelectedLabels();
//                     renderEditExistingLabels();
//                 }
//             }
//         });

//         labelContainer.appendChild(labelText);
//         labelContainer.appendChild(deleteDropdown);
//         editExistingLabelsContainer.appendChild(labelContainer);
//     });
// }

//         // Render label yang sudah dipilih di modal edit
//         function renderEditSelectedLabels() {
//             if (!editSelectedLabelsContainer) return;

//             editSelectedLabelsContainer.innerHTML = "";

//             if (editSelectedLabelIds.length === 0) {
//                 editSelectedLabelsContainer.innerHTML = `
//                     <div class="text-muted small">No labels selected</div>
//                 `;
//                 return;
//             }

//             editSelectedLabelIds.forEach(labelId => {
//                 const label = allLabels.find(l => l.id === labelId);
//                 if (label) {
//                     const bgColor = label.color ? `#${label.color}` : "#E6E5E3";
//                     const textColor = colorMap[label.color] || "#333";

//                     const labelElement = document.createElement("div");
//                     labelElement.classList.add("d-flex", "align-items-center", "gap-2", "rounded-pill", "px-3",
//                         "py-2", "small", "me-2", "mb-2");
//                     labelElement.style.backgroundColor = bgColor;
//                     labelElement.style.color = textColor;
//                     labelElement.style.border = `1px solid ${textColor}22`;

//                     const labelText = document.createElement("span");
//                     labelText.textContent = label.name;

//                     const removeBtn = document.createElement("button");
//                     removeBtn.type = "button";
//                     removeBtn.classList.add("btn", "btn-sm", "p-0", "shadow-none");
//                     removeBtn.innerHTML = '<i class="ph ph-x shadow-none" style="font-size: 12px;"></i>';
//                     removeBtn.style.color = textColor;
//                     removeBtn.style.border = "none";
//                     removeBtn.style.background = "none";
//                     removeBtn.style.cursor = "pointer";

//                     removeBtn.addEventListener("click", (e) => {
//                         e.stopPropagation();
//                         editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== labelId);
//                         renderEditSelectedLabels();
//                         renderEditExistingLabels();
//                     });

//                     labelElement.appendChild(labelText);
//                     labelElement.appendChild(removeBtn);
//                     editSelectedLabelsContainer.appendChild(labelElement);
//                 }
//             });
//         }

//         // 🔘 Klik tombol Add → ganti dengan input
//         if (editAddLabelBtn) {
//             editAddLabelBtn.addEventListener("click", () => {
//                 editAddLabelBtn.classList.add("d-none");
//                 editNewLabelContainer.classList.remove("d-none");
//                 editNewLabelInput.focus();
//             });
//         }

//         // 💾 Simpan label baru di modal edit
//         if (editSaveNewLabelBtn) {
//             editSaveNewLabelBtn.addEventListener("click", async () => {
//                 const name = editNewLabelInput.value.trim();
//                 if (!name) {
//                     showMessage("Label name cannot be empty", "danger", "editFileMessage");
//                     return;
//                 }

//                 // Cek apakah label sudah ada
//                 const existingLabel = allLabels.find(l => l.name.toLowerCase() === name.toLowerCase());
//                 if (existingLabel) {
//                     showMessage("Label with this name already exists", "danger", "editFileMessage");
//                     return;
//                 }

//                 const color = labelColors[Math.floor(Math.random() * labelColors.length)];

//                 try {
//                     const token = getToken();
//                     const res = await fetch("https://pdu-dms.my.id/api/create-label", {
//                         method: "POST",
//                         headers: {
//                             "Content-Type": "application/json",
//                             "Authorization": "Bearer " + token
//                         },
//                         body: JSON.stringify({
//                             name,
//                             color
//                         })
//                     });

//                     if (!res.ok) {
//                         if (res.status === 401) {
//                             window.location.href = "{{ route('signin') }}";
//                             return;
//                         }
//                         throw new Error(`HTTP ${res.status}`);
//                     }

//                     const result = await res.json();

//                     if (result.success === false) {
//                         throw new Error(result.message || "Failed to create label");
//                     }

//                     // Refresh labels
//                     await loadLabels();

//                     // Otomatis pilih label yang baru dibuat
//                     if (result.data && result.data.id) {
//                         editSelectedLabelIds.push(result.data.id);
//                         renderEditSelectedLabels();
//                         renderEditExistingLabels();
//                     }

//                     // Kembalikan ke tombol Add
//                     resetEditAddLabelForm();

//                     showMessage("Label created successfully!", "success", "editFileMessage");

//                 } catch (err) {
//                     console.error('Error creating label:', err);
//                     showMessage("Failed to create label: " + err.message, "danger", "editFileMessage");
//                 }
//             });
//         }

//         // ❌ Cancel pembuatan label baru di modal edit
//         if (editCancelNewLabelBtn) {
//             editCancelNewLabelBtn.addEventListener("click", () => {
//                 resetEditAddLabelForm();
//             });
//         }

//         // Fungsi untuk reset form add label di modal edit
//         function resetEditAddLabelForm() {
//             if (editNewLabelInput) editNewLabelInput.value = "";
//             if (editNewLabelContainer) editNewLabelContainer.classList.add("d-none");
//             if (editAddLabelBtn) editAddLabelBtn.classList.remove("d-none");
//         }

//         // Submit dengan Enter di input label baru
//         if (editNewLabelInput) {
//             editNewLabelInput.addEventListener("keypress", (e) => {
//                 if (e.key === "Enter") {
//                     e.preventDefault();
//                     if (editSaveNewLabelBtn) {
//                         editSaveNewLabelBtn.click();
//                     }
//                 }
//             });
//         }

//         // Export functions untuk digunakan elsewhere
//         window.renderEditExistingLabels = renderEditExistingLabels;
//         window.renderEditSelectedLabels = renderEditSelectedLabels;

//         // Render initial labels
//         renderEditExistingLabels();
//     }

//     // Submit form edit file
//     document.getElementById("editFileForm").addEventListener("submit", async function(e) {
//         e.preventDefault();

//         const token = getToken();
//         if (!token) {
//             showMessage("Session expired. Please login again.", "danger", "editFileMessage");
//             window.location.href = "{{ route('signin') }}";
//             return;
//         }

//         const fileId = document.getElementById("editFileId").value;
//         const title = document.getElementById("editTitle").value.trim();
//         const originalFileName = document.getElementById("editFileNameDisplay").textContent;
//         const fileExtension = originalFileName.split('.').pop();

//         if (!title) {
//             showMessage("Please enter a file name", "danger", "editFileMessage");
//             return;
//         }

//         // Tambahkan extension jika user menghapusnya
//         const finalFileName = title.includes('.') ? title : `${title}.${fileExtension}`;

//         try {
//             const submitBtn = this.querySelector('button[type="submit"]');
//             const originalBtnText = submitBtn.innerHTML;

//             // Tampilkan loading state
//             submitBtn.disabled = true;
//             submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

//             // Siapkan data sesuai format API
//             const requestData = {
//                 name: finalFileName
//             };

//             // Tambahkan labels jika ada (format array of IDs)
//             if (editSelectedLabelIds.length > 0) {
//                 requestData.labels = editSelectedLabelIds;
//             }

//             console.log('Sending update data:', requestData);
//             console.log('File ID:', fileId);

//             // GUNAKAN METHOD PATCH sesuai dokumentasi API
//             const res = await fetch(`https://pdu-dms.my.id/api/update-file/${fileId}`, {
//                 method: "PATCH",
//                 headers: {
//                     "Authorization": "Bearer " + token,
//                     "Content-Type": "application/json",
//                     "Accept": "application/json"
//                 },
//                 body: JSON.stringify(requestData)
//             });

//             console.log('Response status:', res.status);

//             if (!res.ok) {
//                 let errorMessage = `HTTP ${res.status}`;
//                 try {
//                     const errorData = await res.json();
//                     errorMessage = errorData.message || errorMessage;
//                 } catch (parseError) {
//                     console.error('Error parsing error response:', parseError);
//                 }
//                 throw new Error(errorMessage);
//             }

//             const result = await res.json();
//             console.log('Update response:', result);

//             if (result.success === false) {
//                 throw new Error(result.message || "Failed to update file");
//             }

//             showMessage("File updated successfully!", "success", "editFileMessage");

//             // Tutup modal
//             const modal = bootstrap.Modal.getInstance(document.getElementById("editFileModal"));
//             modal.hide();

//             // Refresh halaman untuk menampilkan perubahan
//             setTimeout(() => {
//                 location.reload();
//             }, 1000);

//         } catch (err) {
//             console.error('Error updating file:', err);
//             showMessage("Failed to update file: " + err.message, "danger", "editFileMessage");

//             // Reset button state
//             const submitBtn = this.querySelector('button[type="submit"]');
//             submitBtn.disabled = false;
//             submitBtn.innerHTML = 'Save Changes';
//         }
//     });

//     // Reset form ketika modal ditutup
//     document.getElementById('editFileModal').addEventListener('hidden.bs.modal', function() {
//         // Reset selected labels
//         editSelectedLabelIds = [];

//         // Reset form
//         document.getElementById('editFileForm').reset();

//         // Clear selected labels display
//         const editSelectedLabelsContainer = document.getElementById("editSelectedLabels");
//         if (editSelectedLabelsContainer) {
//             editSelectedLabelsContainer.innerHTML = "";
//         }

//         // Reset add label form
//         const editNewLabelContainer = document.getElementById("editNewLabelContainer");
//         const editAddLabelBtn = document.getElementById("editAddLabelBtn");
//         if (editNewLabelContainer && editAddLabelBtn) {
//             editNewLabelContainer.classList.add("d-none");
//             editAddLabelBtn.classList.remove("d-none");
//         }
//     });
//     // Event listener untuk delete label dari existing labels
// document.addEventListener('click', async (e) => {
//     if (e.target.closest('.delete-existing-label-btn')) {
//         e.preventDefault();
//         e.stopPropagation();

//         const deleteBtn = e.target.closest('.delete-existing-label-btn');
//         const labelId = deleteBtn.getAttribute('data-label-id');
//         const labelName = deleteBtn.getAttribute('data-label-name');

//         if (!confirm(`Are you sure you want to delete label "${labelName}"?`)) {
//             return;
//         }

//         await deleteExistingLabel(labelId, labelName, deleteBtn);
//     }
// });

// // Function untuk delete label
// async function deleteExistingLabel(labelId, labelName, buttonElement) {
//     const token = getToken();
//     if (!token) {
//         showMessage("Session expired. Please login again.", "danger");
//         window.location.href = "{{ route('signin') }}";
//         return;
//     }

//     try {
//         // Show loading state
//         const originalText = buttonElement.innerHTML;
//         buttonElement.innerHTML = '<i class="ph ph-spinner ph-spin fs-5"></i> Deleting...';
//         buttonElement.disabled = true;

//         const response = await fetch(`https://pdu-dms.my.id/api/delete-label/${labelId}`, {
//             method: 'DELETE',
//             headers: {
//                 'Authorization': 'Bearer ' + token,
//                 'Accept': 'application/json'
//             }
//         });

//         const result = await response.json();

//         if (response.ok) {
//             // Show success message
//             showMessage(`Label "${labelName}" deleted successfully!`, "success");

//             // Remove from selected labels if selected
//             selectedLabelIds = selectedLabelIds.filter(id => id !== parseInt(labelId));
//             editSelectedLabelIds = editSelectedLabelIds.filter(id => id !== parseInt(labelId));

//             // Reload labels
//             await loadLabels();

//             // Re-render selected labels
//             renderSelectedLabels();
//             if (window.renderEditSelectedLabels) {
//                 window.renderEditSelectedLabels();
//             }
//         } else {
//             throw new Error(result.message || 'Failed to delete label');
//         }

//     } catch (error) {
//         console.error('Delete label error:', error);
//         showMessage('Failed to delete label: ' + error.message, "danger");
//     } finally {
//         // Reset button state
//         if (buttonElement) {
//             buttonElement.innerHTML = originalText;
//             buttonElement.disabled = false;
//         }
//     }
// }

//     // ==================== INITIALIZATION ====================
//     document.addEventListener("DOMContentLoaded", function() {
//         initializeEditFunctionality();
//     });
</script>
