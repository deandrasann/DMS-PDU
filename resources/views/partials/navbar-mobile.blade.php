
<div class="search-container d-flex flex-row align-items-center  w-100 bg-white ">
    <a class="text-dark p-4 align-items-baseline" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
    <i class="ph ph-list fs-5 mb-3"></i>
    </a>
    <p class="fw-semibold align-center fs-4">@yield('title')</p>
    <div>
    <span class="input-group-text align-items-end bg-white border-0 mb-3">
        <i class="ph ph-magnifying-glass"></i>
    </span>
    </div>
</div>

<div class="d-flex gap-3 mx-4">
    <!-- Card Kiri: 3/4 -->
    <div class="card shadow w-75">
        <div class="card-body">
            <p class="badge text-bg-success text-truncate w-100">AAAAAAAAAAAAAAA</p>
        </div>
    </div>

    <!-- Card Kanan: 1/4 -->
    <div class="card shadow w-25">
        <div class="card-body">
            <p class="badge text-bg-primary text-truncate w-100">BBBBBB</p>
        </div>
    </div>
</div>


<div class="offcanvas offcanvas-start w-75" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <button type="button" class="border-0 bg-white me-4 mt-2" data-bs-dismiss="offcanvas" aria-label="Close">
        <i class="ph ph-x"></i>
    </button>
    <img src="{{ asset('storage/images/logo v2.png') }}"
    alt="Logo Besar"
    id="sidebarLogoBig"
    style="width:120px;">
  </div>
  <div class="offcanvas-body">
    <ul class="nav nav-pills mt-2">
        <li class="nav-item  w-100 ">
            <a href="{{ route('dashboard') }}"
               class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ph ph-bell-simple pe-2 fs-5"></i>
                <span class="sidebar-text fw-normal">Notification</span>
            </a>
        </li>
        <li class="nav-item w-100">
            <hr class="border-2 my-2" style="color: #000">
        </li>
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
