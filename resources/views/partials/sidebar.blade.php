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
                        <label for="label" class="form-label fw-semibold">Label</label>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Dropdown Label -->
                            <select class="form-select rounded-3" id="labelSelect" name="label" style="flex:1;">
                                <option value="">Select Label</option>
                            </select>
                            <!-- Tombol Add -->
                            <button type="button" class="btn btn-outline-primary rounded-3" id="addLabelBtn">
                                + Add
                            </button>
                        </div>

                        <!-- Input Label Baru (hidden default) -->
                        <div id="newLabelContainer" class="mt-2 d-none">
                            <div class="input-group">
                                <input type="text" id="newLabelInput" class="form-control"
                                    placeholder="Enter new label name">
                                <button type="button" class="btn btn-success" id="saveNewLabelBtn">Save</button>
                            </div>
                        </div>

                        <!-- Daftar label preview -->
                        <div id="labelPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
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
                <button type="button" class="btn btn-light border rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-3 px-4 fw-semibold" id="createFolderBtn">Create Folder</button>
            </div>
        </div>
    </div>
</div>
<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmationModal" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background-color: #fff; max-width: 500px;">

            <!-- Close Button (Kanan Atas) -->
            <button type="button"
                    class="btn-close position-absolute top-0 end-0 mt-3 me-3"
                    data-bs-dismiss="modal" aria-label="Close"
                    style="z-index: 10; font-size: 1.1rem; opacity: 0.7;">
            </button>

            <div class="modal-body py-4 px-4">
                <!-- Teks Rata Kiri + Padding Kiri -->
                <div style="padding-left: 1.75rem;">
                    <h5 class="fw-bold mb-2 text-start" style="font-size: 1.25rem;">
                        Are you sure want to leave?
                    </h5>
                    <p class="text-muted small mb-4 text-start" style="font-family: Rubik; line-height: 1.5; font-size: 0.875rem;">
                        You must log in again to access this dashboard.
                    </p>
                </div>

                <!-- Tombol: Cancel (kiri) + Log out (kanan) -->
                <div class="d-flex justify-content-between align-items-center" style="padding-left: 1.75rem; padding-right: 1.5rem;">
                    <button type="button"
                            class="btn btn-outline-secondary rounded-4 px-4 py-2"
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
    document.getElementById("openUploadFolderModal").addEventListener("click", function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById("uploadFolderModal"));
        modal.show();
    });

    document.getElementById("openUploadModal").addEventListener("click", function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById("uploadFileModal"));
        modal.show();
    });

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

    document.addEventListener("DOMContentLoaded", function () {
    const logoutLink = document.getElementById('logoutLink');
    const logoutForm = document.getElementById('logout-form');
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');

    // Buka modal saat klik "Log Out"
    logoutLink.addEventListener('click', function (e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('logoutConfirmationModal'));
        modal.show();
    });

    // Konfirmasi logout → submit form
    confirmLogoutBtn.addEventListener('click', function () {
        logoutForm.submit();
    });
    document.getElementById('logoutConfirmationModal').addEventListener('click', function (e) {
        if (e.target === this) {
            bootstrap.Modal.getInstance(this).hide();
        }
    });
});
</script>

