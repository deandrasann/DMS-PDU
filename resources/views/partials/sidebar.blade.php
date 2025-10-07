<div id="sidebar"
     class="d-flex vh-100 flex-column flex-shrink-0 p-3 bg-light shadow mb-2 rounded-4 position-fixed sidebar-full"
     style="width: 280px; transition: width 0.3s;">
    <div class="d-flex align-items-center justify-content-between mb-3 position-relative">
        <div class="d-flex align-items-center">
            <img src="{{ asset('storage/images/logo v1.png') }}"
                class="d-flex align-items-center ms-2 p-1"
                alt="Logo Kecil"
                id="sidebarLogoSmall"
                style="width:36px;">

            <!-- Logo besar (muncul saat expanded) -->
            <img src="{{ asset('storage/images/logo v2.png') }}"
                alt="Logo Besar"
                id="sidebarLogoBig"
                style="width:120px;">
        </div>
        <!-- Tombol toggle -->
        <button id="toggleBtn" class="btn btn-light ms-2">
            <i class="ph-bold ph-sidebar-simple"></i>
        </button>
    </div>
    <!-- Tombol Upload -->
    <a href="{{ route('dashboard') }}"
       class="shadow mt-4 p-2 text-decoration-none text-dark border rounded-4 d-flex align-items-center mb-2">
        <i class="ph ph-bold ph-plus fs-3 me-2"></i>
        <span class="sidebar-text fw-normal">Upload</span>
    </a>

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
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none">
            <i class="ph ph-sign-out me-2 fs-5"></i>
            <span class="sidebar-text">Log Out</span>
        </a>
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
    
