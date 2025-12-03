<div class="search-container mobile-only d-flex flex-column w-100 bg-white p-3 shadow-sm">
    <!-- 🔹 Baris atas: menu & title -->
    <div class="d-flex flex-row align-items-center mb-3">
        <a class="text-dark me-3" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
            aria-controls="offcanvasExample">
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
            <input type="text" class="form-control border-0 shadow-none p-0 bg-transparent"
                placeholder="Search in DMS PDU" style="font-size: 0.9rem;">

            <!-- Filter icon -->
            <i class="ph ph-sliders-horizontal text-dark ms-2" role="button"></i>
        </div>

        <!-- Sort button -->
        <button class="btn btn-light rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center">
            <i class="ph ph-arrows-down-up text-dark" style="font-size: 1.2rem;"></i>
        </button>
    </div>
</div>

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

                <form id="profileUpdateForm" enctype="multipart/form-data">
                    @csrf

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
                            <button type="button" id="deletePhotoBtn"
                                    class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                <i class="ph ph-trash fs-5"></i>
                            </button>

                            <input type="file" name="photo_profile" id="photoInput" class="d-none" accept="image/*">

                            <button type="button" id="uploadPhotoBtn"
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

                    <!-- Password + Change Password (auto width, di bawah label) -->
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

            <!-- FIXED BOTTOM: Log Out & Save Changes mentok bawah -->
            <div class="position-fixed bottom-0 start-0 end-0 bg-white"
                 style="padding: 16px 16px 34px;">
                <div class="d-flex">
                    <button type="button"
                            onclick="event.preventDefault(); document.getElementById('logoutForm').submit();"
                            class="btn btn-danger rounded-4 fw-semibold"
                            style="height: 50px; font-size: 17px;">
                        Log Out
                    </button>
                    <button type="submit" form="profileUpdateForm"
                            class="btn rounded-4 fw-semibold text-white ms-auto"
                            style="height: 50px; font-size: 17px; background-color: #007AFF;">
                        Save Changes
                    </button>
                </div>
            </div>

            <!-- Hidden Logout Form -->
            <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

        </div>
    </div>
</div>


<!-- Change Password Modal – Dengan Eye Toggle + Button Rata Kiri/Kanan -->
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
                <form id="changePasswordForm">
                    @csrf
                    @method('POST')

                    <!-- Old Password -->
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">Old password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control rounded-3 border-0"
                                placeholder="Insert old password" style="height: 50px; background:#f7f7f8;" required>
                            <span class="input-group-text bg-transparent border-0" style="height: 50px;">
                                <i class="ph ph-eye-slash toggle-password cursor-pointer"
                                    data-target="current_password"></i>
                            </span>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">New password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control rounded-3 border-0"
                                placeholder="Insert new password" style="height: 50px; background:#f7f7f8;" required>
                            <span class="input-group-text bg-transparent border-0" style="height: 50px;">
                                <i class="ph ph-eye-slash toggle-password cursor-pointer"
                                    data-target="new_password"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-medium">Confirm new password</label>
                        <div class="input-group">
                            <input type="password" name="new_password_confirmation"
                                class="form-control rounded-3 border-0" placeholder="Type again new password"
                                style="height: 50px; background:#f7f7f8;" required>
                            <span class="input-group-text bg-transparent border-0" style="height: 50px;">
                                <i class="ph ph-eye-slash toggle-password cursor-pointer"
                                    data-target="new_password_confirmation"></i>
                            </span>
                        </div>
                    </div>

                    <div class="text-start mb-4">
                        <a href="{{ route('forgot') }}" class="text-decoration-none" style="color:#f97316; font-size:14px;">Forgot
                            Password?</a>
                    </div>
                </form>
            </div>

            <div class="position-fixed bottom-0 start-0 end-0 bg-white"
                style="padding: 16px 16px 34px;">

                <!-- Pakai d-flex + ms-auto supaya Change mentok kanan -->
                <div class="d-flex">
                    <!-- Cancel mentok kiri -->
                    <button type="button" class="btn btn-light rounded-4 fw-semibold me-3"
                        style="height: 50px; font-size: 17px; min-width: 120px;" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <!-- Change mentok kanan (ms-auto = margin-start auto) -->
                    <button type="submit" form="changePasswordForm"
                        class="btn rounded-4 fw-semibold text-white ms-auto"
                        style="height: 50px; font-size: 17px; background-color: #007AFF; min-width: 120px;">
                        Change
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
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