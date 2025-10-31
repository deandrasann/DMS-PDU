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
    <a href="#"
       class="shadow p-2 text-decoration-none text-dark border rounded-4 d-flex align-items-center"
       id="uploadDropdown"
       data-bs-toggle="dropdown"
       aria-expanded="false">
        <i class="ph ph-bold ph-plus fs-3 me-2 my-2"></i>
        <span class="sidebar-text fw-normal">Upload</span>
    </a>

    <ul class="dropdown-menu border-0 shadow-sm rounded-4" aria-labelledby="uploadDropdown">
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="ph ph-upload-simple fs-5"></i> Upload File
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="#" id="new-folder-btn">
                <i class="ph ph-folder fs-5"></i> New Folder
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
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="ph ph-sign-out me-2 fs-5"></i>
            <span class="sidebar-text">Log Out</span>
        </a>
        <!-- Hidden Logout Form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true"   data-bs-backdrop="false" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="uploadModalLabel">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Upload Form -->
                <form id="uploadForm">
                    @csrf
                    <div class="upload-box text-center mb-4 border-2 border-dashed rounded-3 p-4" id="uploadArea"
                        style="border-color: #e9ecef; cursor: pointer;">
                        <input type="file" name="file" id="fileInput" class="d-none"
                            accept=".csv,.docx,.pdf,.pptx,.xlsx">
                        <div class="bg-light-orange bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:60px; height:60px;">
                            <i class="ph ph-upload-simple text-dark fs-3" id="uploadIcon"></i>
                        </div>
                        <p class="mb-1 text-secondary">
                            Drag & drop files or <span class="text-decoration-none text-orange">click here</span>
                        </p>
                        <small class="text-muted">Supported file types: CSV, DOCX, PDF, PPTX, XLSX</small>
                        <div id="fileName" class="mt-2 text-primary fw-semibold"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Save as</label>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="Title Goes Here (Optional)">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Label</label>
                        <select class="form-select" name="label" id="label">
                            <option value="" selected>Add Label</option>
                            <option value="work">Work</option>
                            <option value="personal">Personal</option>
                            <option value="school">School</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
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
</script>
