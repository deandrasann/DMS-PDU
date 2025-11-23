
<div class="search-container d-flex flex-column w-100 bg-white p-3 shadow-sm">
    <!-- 🔹 Baris atas: menu & title -->
    <div class="d-flex flex-row align-items-center mb-3">
        <a class="text-dark me-3" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
            <i class="ph ph-list fs-4"></i>
        </a>
        <p class="fw-semibold fs-4 mb-0">@yield('title')</p>
    </div>

    <!-- 🔹 Baris bawah: search bar -->
    <div class="d-flex align-items-center gap-2 w-100">
        <div class="d-flex align-items-center bg-white rounded-pill shadow-sm px-3 py-2 flex-grow-1">
            <!-- Search icon -->
            <i class="ph ph-magnifying-glass text-dark me-2"></i>

            <!-- Input -->
            <input type="text"
                   class="form-control border-0 shadow-none p-0 bg-transparent"
                   placeholder="Search in DMS PDU"
                   style="font-size: 0.9rem;">

            <!-- Filter icon -->
            <i class="ph ph-sliders-horizontal text-dark ms-2" role="button"></i>
        </div>

        <!-- Sort button -->
        <button class="btn btn-light rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center">
            <i class="ph ph-arrows-down-up text-dark" style="font-size: 1.2rem;"></i>
        </button>
    </div>
</div>




<div class="offcanvas offcanvas-start w-75" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <button type="button" class="border-0 bg-white me-4 mt-2" data-bs-dismiss="offcanvas" aria-label="Close">
        <i class="ph ph-x"></i>
    </button>
    <img src="{{ asset('img/logo v2.png') }}"
    alt="Logo Besar"
    id="sidebarLogoBig"
    style="width:120px;">
  </div>
  <div class="offcanvas-body">
    <ul class="nav nav-pills mt-2">
        <li class="nav-item w-100">
            <hr class="border-2 my-2" style="color: #000">
        </li>
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
               class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('myspace') ? 'active' : '' }}">
                <i class="ph ph-folder-user me-2 fs-5"></i>
                <span class="sidebar-text">My Space</span>
            </a>
        </li>
        <li class="nav-item w-100">
            <hr class="border-2 my-2" style="color: #000">
        </li>
        <li class="nav-item w-100">
            <a href="{{ route('shared') }}"
               class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('shared') ? 'active' : '' }}">
                <i class="ph ph-users-three me-2 fs-5"></i>
                <span class="sidebar-text">Shared With Me</span>
            </a>
        </li>
    </ul>
  </div>
</div>
