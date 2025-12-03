<div class="search-container mobile-only d-flex flex-column w-100 bg-white p-3 shadow-sm">
    <!-- 🔹 Baris atas: menu & title -->
    <div class="d-flex flex-row align-items-center mb-3">
        <a class="text-dark me-3" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
            aria-controls="offcanvasExample">
            <i class="ph ph-list fs-4"></i>
        </a>
        <p class="fw-semibold fs-4 mb-0">@yield('title')</p>
    </div>

    <!-- 🔹 Baris bawah: search bar dengan filter dan sort -->
    <div class="d-flex align-items-center gap-2 w-100 position-relative">
        <!-- Search Box -->
        <div class="d-flex align-items-center bg-white rounded-pill shadow-sm px-3 py-2 flex-grow-1 position-relative"
             style="border: 1px solid #e0e0e0;">
            <!-- Search icon -->
            <i class="ph ph-magnifying-glass text-dark me-2"></i>

            <!-- Input -->
            <input type="text" id="mobileSearchInput" class="form-control border-0 shadow-none p-0 bg-transparent"
                placeholder="Search in DMS PDU" style="font-size: 0.9rem;">

            <!-- Filter icon -->
            <i class="ph ph-sliders-horizontal text-dark ms-2 filter-toggle-mobile" role="button"></i>
        </div>

        <!-- Floating Filter Panel untuk Mobile -->
        <div class="filter-panel-mobile mt-3" id="mobileFilterPanel" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; background: white; margin-top: 5px;">
            <div class="card card-body rounded-4 shadow-sm border-0" style="background: white;">

                <!-- DATE MODIFIED -->
                <div class="filter-row mb-3">
                    <label class="form-label text-secondary fw-medium mb-2">Date Modified</label>
                    <div class="dropdown-container">
                        <button class="dropdown-toggle-custom-mobile d-flex justify-content-between align-items-center w-100 border rounded-3 p-2 bg-light"
                                data-target="#mobileDd1">
                            <span>Any Time</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu-custom-mobile border rounded-3 shadow-sm mt-1" id="mobileDd1" style="display: none; background: white;">
                            <div class="item p-2 border-bottom" data-value="" data-filter-type="date_modified">Any Time</div>
                            <div class="item p-2 border-bottom" data-value="today" data-filter-type="date_modified">Today</div>
                            <div class="item p-2 border-bottom" data-value="last_week" data-filter-type="date_modified">Last Week</div>
                            <div class="item p-2 border-bottom" data-value="last_month" data-filter-type="date_modified">Last Month</div>
                            <div class="item p-2" data-value="last_year" data-filter-type="date_modified">Last Year</div>
                        </div>
                    </div>
                </div>

                <!-- TYPE -->
                <div class="filter-row mb-3">
                    <label class="form-label text-secondary fw-medium mb-2">Type</label>
                    <div class="dropdown-container">
                        <button class="dropdown-toggle-custom-mobile d-flex justify-content-between align-items-center w-100 border rounded-3 p-2 bg-light"
                                data-target="#mobileDd2">
                            <span>Any Type</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu-custom-mobile border rounded-3 shadow-sm mt-1" id="mobileDd2" style="display: none; background: white;">
                            <div class="item p-2 border-bottom" data-value="" data-filter-type="type">Any Type</div>
                            <div class="item p-2 border-bottom d-flex align-items-center" data-value="PDF" data-filter-type="type">
                                <i class="ph ph-file-pdf me-2 text-danger"></i>PDF
                            </div>
                            <div class="item p-2 border-bottom d-flex align-items-center" data-value="Spreadsheet" data-filter-type="type">
                                <i class="ph ph-file-xls me-2 text-success"></i>Spreadsheet
                            </div>
                            <div class="item p-2 border-bottom d-flex align-items-center" data-value="Document" data-filter-type="type">
                                <i class="ph ph-file-doc me-2 text-primary"></i>Document
                            </div>
                            <div class="item p-2 d-flex align-items-center" data-value="Image" data-filter-type="type">
                                <i class="ph ph-file-image me-2 text-warning"></i>Image
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LABEL -->
                <div class="filter-row">
                    <label class="form-label text-secondary fw-medium mb-2">Label</label>
                    <div class="dropdown-container">
                        <button class="dropdown-toggle-custom-mobile d-flex justify-content-between align-items-center w-100 border rounded-3 p-2 bg-light"
                                data-target="#mobileDd4">
                            <span>Any Label</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu-custom-mobile border rounded-3 shadow-sm mt-1" id="mobileDd4" style="display: none; background: white; max-height: 300px; overflow-y: auto;">
                            <div class="item p-2 border-bottom" data-value="" data-filter-type="label">Any Label</div>
                            <div id="mobileLabelsContainer" class="p-2 d-flex flex-wrap gap-1">
                                <!-- Labels akan dimuat di sini -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                    <button class="btn btn-outline-secondary btn-sm rounded-3 px-3" id="mobileResetFilters">
                        Reset
                    </button>
                    <button class="btn btn-primary btn-sm rounded-3 px-3" id="mobileApplyFilters">
                        Apply
                    </button>
                </div>
            </div>
        </div>

        <!-- Sort Button -->
        <div class="sort-box-mobile position-relative">
            <button class="btn btn-light rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center sort-toggle-mobile"
                style="width: 45px; height: 45px; border: 1px solid #e0e0e0;">
                <i class="ph ph-arrows-down-up text-dark" style="font-size: 1.2rem;"></i>
            </button>

            <!-- Sort Dropdown Menu -->
            <div class="dropdown-menu-mobile shadow border-0 rounded-4 p-2"
                 style="display: none; position: absolute; right: 0; top: 100%; min-width: 200px; z-index: 1050; background: white; margin-top: 5px;">
                <a class="dropdown-item d-flex align-items-center gap-2 sort-option-mobile p-2 border-bottom" href="#"
                    data-sort="alphabetical">
                    <i class="ph ph-sort-ascending me-2"></i> Alphabetical
                </a>
                <a class="dropdown-item d-flex align-items-center gap-2 sort-option-mobile p-2 border-bottom" href="#"
                    data-sort="reverse_alphabetical">
                    <i class="ph ph-sort-descending me-2"></i> Reverse Alphabetical
                </a>
                <div class="dropdown-divider my-1"></div>
                <a class="dropdown-item d-flex align-items-center gap-2 sort-option-mobile p-2 border-bottom" href="#" data-sort="latest">
                    <i class="ph ph-clock me-2"></i> Latest
                </a>
                <a class="dropdown-item d-flex align-items-center gap-2 sort-option-mobile p-2 border-bottom" href="#" data-sort="oldest">
                    <i class="ph ph-clock-counter-clockwise me-2"></i> Oldest
                </a>
                <div class="dropdown-divider my-1"></div>
                <a class="dropdown-item d-flex align-items-center gap-2 sort-option-mobile p-2 border-bottom" href="#" data-sort="smallest">
                    <i class="ph ph-arrow-down me-2"></i> Smallest
                </a>
                <a class="dropdown-item d-flex align-items-center gap-2 sort-option-mobile p-2" href="#" data-sort="largest">
                    <i class="ph ph-arrow-up me-2"></i> Largest
                </a>
            </div>
        </div>

        <!-- Notification Button -->
        <div class="notif-box-mobile">
            <button class="btn btn-light rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center"
                style="width: 45px; height: 45px; border: 1px solid #e0e0e0;">
                <i class="ph ph-bell text-dark fs-5"></i>
            </button>
        </div>
    </div>

    <!-- 🔹 Status bar untuk menunjukkan filter aktif -->
    <div id="mobileActiveFilters" class="mt-2 d-flex flex-wrap gap-1" style="display: none !important;">
        <!-- Filter chips akan muncul di sini -->
    </div>
</div>

<!-- Offcanvas Menu (Sidebar Mobile) -->
<div class="offcanvas mobile-only offcanvas-start w-75" tabindex="-1" id="offcanvasExample"
    aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <button type="button" class="border-0 bg-white me-4 mt-2" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ph ph-x"></i>
        </button>
        <img src="{{ asset('img/logo v2.png') }}" alt="Logo Besar" id="sidebarLogoBig" style="width:120px;">
    </div>

    <div class="offcanvas-body d-flex flex-column justify-content-between">
        <!-- MENU UTAMA -->
        <ul class="nav nav-pills mt-2">
            <li class="nav-item w-100">
                <hr class="border-2 my-2" style="color: #000">
            </li>
            <li class="nav-item w-100">
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

        <!-- BAGIAN BAWAH: PROFILE + ACCOUNT SETTINGS -->
        <div class="mt-auto">
            <div class="card border-0 shadow-sm rounded-3 p-2 mb-3">
                <div class="d-flex align-items-center">
                    <!-- Foto Profil -->
                    <div class="me-2 flex-shrink-0">
                        <img id="mobileProfilePhoto"
                            src="{{ session('new_profile_photo') ??
                                (session('user.photo_profile_path')
                                    ? 'https://pdu-dms.my.id/storage/profile_photos/' . session('user.photo_profile_path')
                                    : asset('storage/images/profile-pict.jpg')) }}?v={{ time() }}"
                            alt="Profile" class="rounded-circle object-fit-cover" style="width:48px;height:48px;">
                    </div>

                    <!-- Nama + Email -->
                    <div class="flex-grow-1 min-width-0">
                        <div id="mobileProfileName" class="fw-semibold text-dark"
                            style="font-size:15px;line-height:1.3;">
                            {{ session('user.fullname') ?? (session('user.name') ?? 'User') }}
                        </div>
                        <div id="mobileProfileEmail" class="text-muted small text-truncate" style="font-size:13px;">
                            {{ session('user.email') ?? 'loading@email.com' }}
                        </div>
                    </div>



                    <!-- Gear Icon -->
                    <button type="button"
                        class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center flex-shrink-0"
                        data-bs-toggle="modal" data-bs-target="#mobileAccountSettingsModal"
                        style="width:40px;height:40px;">
                        <i class="ph ph-gear text-dark" style="font-size:1.3rem;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Account Settings Modal untuk Mobile -->
<div class="modal fade mobile-only" id="mobileAccountSettingsModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered">
        <div class="modal-content border-0" style="min-height: 100vh; margin: 0;">

            <!-- Header: X dan Title Sejajar Horizontal -->
            <div class="d-flex align-items-center justify-content-center position-relative py-4 bg-white">
                <button type="button" class="btn-close position-absolute start-0 ms-4" data-bs-dismiss="modal"></button>
                <h5 class="modal-title fw-bold mb-0 fs-5">Account Settings</h5>
            </div>

            <!-- Body Form -->
            <div class="modal-body px-3 pb-5">

                <form id="mobileProfileUpdateForm" action="{{ route('profile.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">

                    <!-- Profile Photo Section -->
                    <div class="text-center my-4">
                        <img id="modalProfilePhoto"
                             src="{{ session('new_profile_photo') ??
                                   (session('user.photo_profile_path')
                                       ? 'https://pdu-dms.my.id/storage/profile_photos/' . session('user.photo_profile_path')
                                       : asset('storage/images/profile-pict.jpg')) }}?v={{ time() }}"
                             class="rounded-circle object-fit-cover"
                             width="110" height="110">

                        <p class="text-muted small mt-3 mb-1">Upload an Image</p>
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">Max file size: 10MB</p>

                        <!-- Delete (bulat) + Upload (persegi, auto width) -->
                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <button type="button" id="mobileDeletePhotoBtn"
                                    class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                <i class="ph ph-trash fs-5"></i>
                            </button>

                            <input type="file" name="photo_profile" id="mobilePhotoInput" class="d-none" accept="image/*">

                            <button type="button" id="mobileUploadPhotoBtn"
                                    class="btn btn-outline-secondary rounded-4 px-4"
                                    style="height: 44px; font-size: 16px; max-width: fit-content; white-space: nowrap;">
                                Upload
                            </button>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">Full Name</label>
                        <input type="text" name="fullname" class="form-control rounded-3"
                               value="{{ old('fullname', session('user.fullname') ?? session('user.name')) }}"
                               style="height: 50px; background:#f7f7f8; border:none;" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">Email</label>
                        <input type="email" class="form-control rounded-3 bg-light" readonly
                               value="{{ session('user.email') }}"
                               style="height: 50px; background:#f7f7f8; border:none; color:#8e8e93;">
                    </div>

                    <!-- Password + Change Password -->
                    <div class="mb-5">
                        <label class="form-label text-secondary fw-medium">Password</label>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-light fw-medium px-0"
                                    data-bs-toggle="modal" data-bs-target="#mobileChangePasswordModal"
                                    style="white-space: nowrap; text-decoration: none; font-size: 16px;">
                                Change Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>


            <!-- FIXED BOTTOM: Log Out & Save Changes -->
            <div class="position-fixed bottom-0 start-0 end-0 bg-white"
                 style="padding: 16px 16px 34px;">
                <div class="d-flex">
                    <button type="button"
                            onclick="event.preventDefault(); document.getElementById('mobileLogoutForm').submit();"
                            class="btn btn-danger rounded-4 fw-semibold"
                            style="height: 50px; font-size: 17px;">
                        Log Out
                    </button>
                    <button type="submit" form="mobileProfileUpdateForm"
                            class="btn rounded-4 fw-semibold text-white ms-auto"
                            style="height: 50px; font-size: 17px; background-color: #007AFF;">
                        Save Changes
                    </button>
                </div>
            </div>

            <!-- Hidden Logout Form -->
            <form id="mobileLogoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

        </div>
    </div>
</div>

<!-- Delete Confirmation Modal untuk Mobile -->
<div class="modal fade mobile-only" id="mobileDeleteConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg"
            style="background-color: #fff; max-width: 500px;">

            <!-- Close Button -->
            <button type="button"
                class="btn-close position-absolute top-0 end-0 mt-3 me-3"
                data-bs-dismiss="modal" aria-label="Close"
                style="z-index: 10; font-size: 1.1rem; opacity: 0.7;">
            </button>

            <div class="modal-body py-4 px-5">
                <!-- Teks -->
                <div style="padding-left: 1.75rem; padding-top:1rem;">
                    <h5 class="fw-semibold mb-2 text-start"
                        style="font-size: 1.25rem;">
                        Delete profile picture?
                    </h5>
                    <p class="text-muted small mb-4 text-start"
                        style="font-family: Rubik; line-height: 1.5; font-size: 0.875rem;">
                        This action cannot be undone
                    </p>
                </div>

                <!-- Tombol: Cancel + Delete -->
                <div class="d-flex justify-content-between align-items-center"
                    style="padding-left: 1.75rem;">
                    <button type="button"
                        class="btn btn-outline-secondary rounded-4 px-4 py-2"
                        data-bs-dismiss="modal"
                        style="min-width: 100px; font-size: 14px; border-color: #d1d5db; color: #6b7280;">
                        Cancel
                    </button>
                    <button type="button" id="mobileConfirmDeleteBtn"
                        class="btn btn-danger rounded-4 px-4 py-2 text-white"
                        style="min-width: 100px; font-size: 14px; background-color: #dc3545; border: none;">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal untuk Mobile -->
<div class="modal fade mobile-only" id="mobileChangePasswordModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered">
        <div class="modal-content border-0" style="min-height: 100vh; margin: 0;">

            <!-- Header -->
            <div class="d-flex align-items-center justify-content-center position-relative py-4 bg-white">
                <button type="button" class="btn-close position-absolute start-0 ms-4"
                    data-bs-dismiss="modal"></button>
                <h5 class="modal-title fw-bold mb-0 fs-5">Change Password</h5>
            </div>

            <!-- Form Body -->
            <div class="modal-body px-4 pt-3 pb-5">
                <form id="mobileChangePasswordForm" action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('POST')

                    <!-- Old Password -->
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control rounded-3 border-0"
                                placeholder="Enter old password" style="height: 50px; background:#f7f7f8;" required>
                            <span class="input-group-text bg-transparent border-0" style="height: 50px;">
                                <i class="ph ph-eye-slash toggle-password-mobile cursor-pointer"
                                    data-target="current_password"></i>
                            </span>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control rounded-3 border-0"
                                placeholder="Enter new password" style="height: 50px; background:#f7f7f8;" required>
                            <span class="input-group-text bg-transparent border-0" style="height: 50px;">
                                <i class="ph ph-eye-slash toggle-password-mobile cursor-pointer"
                                    data-target="new_password"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-medium">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password_confirmation"
                                class="form-control rounded-3 border-0" placeholder="Confirm new password"
                                style="height: 50px; background:#f7f7f8;" required>
                            <span class="input-group-text bg-transparent border-0" style="height: 50px;">
                                <i class="ph ph-eye-slash toggle-password-mobile cursor-pointer"
                                    data-target="new_password_confirmation"></i>
                            </span>
                        </div>
                    </div>

                    <div class="text-start mb-4">
                        <a href="{{ route('forgot') }}" class="text-decoration-none" style="color:#f97316; font-size:14px;">Forgot Password?</a>
                    </div>
                </form>
            </div>

            <!-- Fixed Bottom Buttons -->
            <div class="position-fixed bottom-0 start-0 end-0 bg-white"
                style="padding: 16px 16px 34px;">
                <div class="d-flex">
                    <button type="button" class="btn btn-light rounded-4 fw-semibold me-3"
                        style="height: 50px; font-size: 17px; min-width: 120px;" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" form="mobileChangePasswordForm"
                        class="btn rounded-4 fw-semibold text-white ms-auto"
                        style="height: 50px; font-size: 17px; background-color: #007AFF; min-width: 120px;">
                        Change
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Loading Overlay untuk Auto Logout (Mobile) -->
<div id="mobileAutoLogoutLoader"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); backdrop-filter:blur(2px); z-index:9999; align-items:center; justify-content:center;">
    <div class="spinner-border text-light" style="width:3rem; height:3rem;"></div>
</div>


<script>
// ===================================================================
// FUNGSIONALITAS NAVBAR MOBILE - FIXED VERSION
// ===================================================================
function initMobileNavbar() {
    const $ = (s) => document.querySelector(s);
    const $$ = (s) => document.querySelectorAll(s);

    console.log('🔄 Mobile navbar initializing...');

    // ========================================
    // 1. CHECK MySpaceManager
    // ========================================
    const mySpaceManager = window.mySpaceManager;
    if (!mySpaceManager) {
        // console.warn('⚠️ MySpaceManager not found, retrying...');
        setTimeout(initMobileNavbar, 500);
        return;
    }

    console.log('✅ MySpaceManager found!');

    // ========================================
    // 2. FILTER PANEL - FIXED TOGGLE
    // ========================================
    const filterToggleMobile = $('.filter-toggle-mobile');
    const filterPanelMobile = $('#mobileFilterPanel');

    if (filterToggleMobile && filterPanelMobile) {
        console.log('🔧 Filter toggle found');

        filterToggleMobile.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎯 Filter toggle clicked');

            // Gunakan kedua cara untuk memastikan tampil
            if (filterPanelMobile.style.display === 'block' ||
                filterPanelMobile.classList.contains('show')) {
                // Hide
                filterPanelMobile.style.display = 'none';
                filterPanelMobile.classList.remove('show');
                console.log('📦 Filter panel hidden');
            } else {
                // Show - HAPUS SEMUA display:none DULU
                filterPanelMobile.style.display = '';
                filterPanelMobile.classList.remove('d-none');

                // Tampilkan dengan kedua cara
                filterPanelMobile.style.display = 'block';
                filterPanelMobile.classList.add('show');

                // Force reflow untuk memastikan CSS diterapkan
                filterPanelMobile.offsetHeight;

                console.log('📦 Filter panel shown');
            }

            // Close other dropdowns
            closeAllMobileDropdowns();
            const sortDropdown = $('.dropdown-menu-mobile');
            if (sortDropdown) {
                sortDropdown.style.display = 'none';
                sortDropdown.classList.remove('show');
            }
        });

        // Klik di luar untuk tutup
        document.addEventListener('click', (e) => {
            if (filterPanelMobile &&
                !filterPanelMobile.contains(e.target) &&
                !e.target.classList.contains('filter-toggle-mobile') &&
                !e.target.closest('.filter-toggle-mobile')) {

                filterPanelMobile.style.display = 'none';
                filterPanelMobile.classList.remove('show');
            }
        });
    }

    // ========================================
    // 3. DROPDOWNS - FIXED TOGGLE
    // ========================================
    function closeAllMobileDropdowns() {
        console.log('🔒 Closing all dropdowns');
        $$('.dropdown-menu-custom-mobile').forEach(menu => {
            if (menu) {
                menu.style.display = 'none';
                menu.classList.remove('show');
            }
        });
        $$('.dropdown-toggle-custom-mobile').forEach(btn => {
            if (btn) btn.classList.remove("active");
        });
    }

    // Toggle dropdown mobile - SIMPLIFIED
    $$('.dropdown-toggle-custom-mobile').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const targetId = this.dataset.target;
            const menu = $(targetId);
            if (!menu) {
                console.error('❌ Menu not found:', targetId);
                return;
            }

            console.log('🎯 Dropdown toggle clicked:', targetId);

            // Check if currently open
            const isOpen = menu.style.display === 'block' || menu.classList.contains('show');

            // Close all first
            closeAllMobileDropdowns();
            if (filterPanelMobile) {
                filterPanelMobile.style.display = 'none';
                filterPanelMobile.classList.remove('show');
            }
            const sortDropdown = $('.dropdown-menu-mobile');
            if (sortDropdown) {
                sortDropdown.style.display = 'none';
                sortDropdown.classList.remove('show');
            }

            // Toggle current
            if (!isOpen) {
                // Show dropdown - CLEAR semua display restrictions dulu
                menu.style.display = '';
                menu.classList.remove('d-none');

                // Apply display block dan class show
                menu.style.display = 'block';
                menu.classList.add('show');
                this.classList.add("active");

                // Force reflow
                menu.offsetHeight;

                console.log('📦 Dropdown opened:', targetId);
            } else {
                menu.style.display = 'none';
                menu.classList.remove('show');
                this.classList.remove("active");
            }
        });
    });

    // Item click handlers
    $$('.dropdown-menu-custom-mobile .item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const menu = this.closest('.dropdown-menu-custom-mobile');
            if (!menu) return;

            const btn = $(`[data-target="#${menu.id}"]`);

            if (btn) {
                const span = btn.querySelector("span");
                if (span) {
                    span.textContent = this.textContent.trim();
                }

                // Close dropdown
                menu.style.display = 'none';
                menu.classList.remove('show');
                btn.classList.remove("active");

                // Apply filter
                const filterType = this.getAttribute('data-filter-type') || 'value';
                const filterValue = this.getAttribute('data-value') || this.textContent.trim();

                if (mySpaceManager && mySpaceManager.applyFilter) {
                    mySpaceManager.applyFilter(filterType, filterValue);
                }

                // Close filter panel
                if (filterPanelMobile) {
                    filterPanelMobile.style.display = 'none';
                    filterPanelMobile.classList.remove('show');
                }
            }
        });
    });

    // ========================================
    // 4. SEARCH FUNCTIONALITY
    // ========================================
    const mobileSearchInput = $('#mobileSearchInput');
    if (mobileSearchInput && mySpaceManager.applyFilter) {
        let debounceTimer;

        mobileSearchInput.addEventListener('input', (e) => {
            const keyword = e.target.value.trim();
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                mySpaceManager.applyFilter('search', keyword);
            }, 500);
        });

        mobileSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                clearTimeout(debounceTimer);
                const keyword = e.target.value.trim();
                mySpaceManager.applyFilter('search', keyword);
            }
        });
    }

    // ========================================
    // 5. SORT DROPDOWN - FIXED
    // ========================================
    const sortToggleMobile = $('.sort-toggle-mobile');
    const sortDropdownMobile = $('.dropdown-menu-mobile');

    if (sortToggleMobile && sortDropdownMobile && mySpaceManager.loadFilesAndFolders) {
        sortToggleMobile.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Check if open
            const isOpen = sortDropdownMobile.style.display === 'block' ||
                          sortDropdownMobile.classList.contains('show');

            // Close other dropdowns
            closeAllMobileDropdowns();
            if (filterPanelMobile) {
                filterPanelMobile.style.display = 'none';
                filterPanelMobile.classList.remove('show');
            }

            // Toggle sort dropdown
            if (!isOpen) {
                // Clear display restrictions
                sortDropdownMobile.style.display = '';
                sortDropdownMobile.classList.remove('d-none');

                // Show
                sortDropdownMobile.style.display = 'block';
                sortDropdownMobile.classList.add('show');
                console.log('📦 Sort dropdown opened');
            } else {
                sortDropdownMobile.style.display = 'none';
                sortDropdownMobile.classList.remove('show');
            }
        });

        // Sort option selection
        $$('.sort-option-mobile').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const sortType = this.dataset.sort;
                console.log('🔀 Sorting by:', sortType);

                // Close dropdown
                sortDropdownMobile.style.display = 'none';
                sortDropdownMobile.classList.remove('show');

                // Apply sort
                mySpaceManager.loadFilesAndFolders(sortType);
            });
        });
    }

    // ========================================
    // 6. RESET & APPLY BUTTONS
    // ========================================
    $('#mobileResetFilters')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Reset UI
        $$('.dropdown-toggle-custom-mobile span').forEach(span => {
            if (!span) return;
            if (span.closest('[data-target="#mobileDd1"]')) span.textContent = 'Any Time';
            else if (span.closest('[data-target="#mobileDd2"]')) span.textContent = 'Any Type';
            else if (span.closest('[data-target="#mobileDd4"]')) span.textContent = 'Any Label';
        });

        if (mobileSearchInput) mobileSearchInput.value = '';

        // Reset filters
        if (mySpaceManager.activeFilters) {
            mySpaceManager.activeFilters = {
                date_modified: '',
                type: '',
                label: '',
                search: ''
            };
            mySpaceManager.loadFilesAndFolders();
        }

        // Close panels
        closeAllMobileDropdowns();
        if (filterPanelMobile) {
            filterPanelMobile.style.display = 'none';
            filterPanelMobile.classList.remove('show');
        }
    });

    $('#mobileApplyFilters')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (filterPanelMobile) {
            filterPanelMobile.style.display = 'none';
            filterPanelMobile.classList.remove('show');
        }
    });

    // ========================================
    // 7. INITIALIZATION COMPLETE
    // ========================================
    console.log('✅ Mobile navbar fully initialized');
    console.log('📊 Elements ready:');
    console.log('- Filter toggle:', !!filterToggleMobile);
    console.log('- Filter panel:', !!filterPanelMobile);
    console.log('- Search input:', !!mobileSearchInput);
    console.log('- Sort toggle:', !!sortToggleMobile);
    console.log('- Sort dropdown:', !!sortDropdownMobile);
}

// Start initialization
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(initMobileNavbar, 100);
    });
} else {
    setTimeout(initMobileNavbar, 100);
}
</script>
{{-- <script>
    // Pastikan DOM sudah siap
    document.addEventListener('DOMContentLoaded', function () {
        const $ = (selector) => document.querySelector(selector);
        const $$ = (selector) => document.querySelectorAll(selector);

        // ========================================
        // 1. TOGGLE SHOW/HIDE PASSWORD (Eye Icon)
        // ========================================
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const targetName = this.getAttribute('data-target');
                const input = document.querySelector(`input[name="${targetName}"]`);
                if (!input) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.replace('ph-eye-slash', 'ph-eye');
                } else {
                    input.type = 'password';
                    this.classList.replace('ph-eye', 'ph-eye-slash');
                }
            });
        });

        // ========================================
        // 2. UPLOAD & PREVIEW FOTO PROFIL
        // ========================================
        const photoInput = $('#photoInput');
        const uploadBtn = $('#uploadPhotoBtn');
        const modalPhoto = $('#modalProfilePhoto');
        const sidebarPhoto = $('#mobileProfilePhoto');

        if (uploadBtn && photoInput) {
            uploadBtn.addEventListener('click', () => photoInput.click());

            photoInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const newSrc = e.target.result;
                        if (modalPhoto) modalPhoto.src = newSrc;
                        if (sidebarPhoto) sidebarPhoto.src = newSrc;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // ========================================
        // 3. DELETE PROFILE PHOTO
        // ========================================
        $('#deletePhotoBtn')?.addEventListener('click', async function () {
            if (!confirm('Yakin ingin menghapus foto profil?')) return;

            try {
                const { data } = await api.post('{{ route('profile.delete.photo') }}');
                const defaultUrl = data.photo_url + '?t=' + Date.now();

                [modalPhoto, sidebarPhoto].forEach(img => {
                    if (img) img.src = defaultUrl;
                });

                alert('Foto profil berhasil dihapus!');
            } catch (err) {
                alert(err.response?.data?.message || 'Gagal menghapus foto');
            }
        });

        // ========================================
        // 4. UPDATE PROFILE (Nama + Foto)
        // ========================================
        const profileForm = $('#profileUpdateForm');
        if (profileForm) {
            profileForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

                const formData = new FormData(this);

                try {
                    const { data } = await api.post('{{ route('profile.update') }}', formData);

                    // Update foto di modal & sidebar
                    const newPhotoUrl = (data.photo_url || data.photo_path) + '?t=' + Date.now();
                    [modalPhoto, sidebarPhoto].forEach(img => {
                        if (img) img.src = newPhotoUrl;
                    });

                    // Update nama di sidebar
                    $$('#mobileProfileName').forEach(el => {
                        if (el) el.textContent = data.fullname || data.name;
                    });

                    // Tutup modal
                    const modalEl = document.getElementById('mobileAccountSettingsModal');
                    bootstrap.Modal.getInstance(modalEl)?.hide();

                    alert('Profil berhasil diperbarui!');

                } catch (err) {
                    const msg = err.response?.data?.message || 'Gagal memperbarui profil';
                    alert(msg);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        // ========================================
        // 5. CHANGE PASSWORD
        // ========================================
        const changePassForm = $('#changePasswordForm');
        if (changePassForm) {
            changePassForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

                const formData = new FormData(this);

                try {
                    await api.post('{{ route('password.update') }}', formData);
                    alert('Password berhasil diubah! Anda akan keluar dalam 2 detik...');
                    setTimeout(() => location.href = '/signin', 2000);
                } catch (err) {
                    const msg = err.response?.data?.message || 'Gagal mengubah password';
                    alert(msg);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    });
</script> --}}
