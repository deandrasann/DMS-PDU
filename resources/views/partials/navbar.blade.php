<div class="search-container d-flex flex-row align-items-center w-100 mb-2">
    <!-- Search Box -->
    <div class="position-relative w-100">
        <!-- Search Box -->
        <div class="search-box rounded-2">
            <div class="input-group shadow-sm rounded-2 bg-white">
                <span class="input-group-text bg-white border-0">
                    <i class="ph ph-magnifying-glass"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search in DMS PDU">
                <span class="input-group-text bg-white border-0 filter-toggle" style="cursor:pointer;">
                    <i class="ph ph-sliders-horizontal fs-5"></i>
                </span>
            </div>
        </div>

        <!-- Floating Filter Panel -->
        <div class="filter-panel mt-3" id="filterPanel" style="display: none;">

            <div class="card card-body rounded-4 shadow-sm">

                <!-- DATE MODIFIED -->
                <div class="filter-row">
                    <label>Date Modified</label>

                    <div class="dropdown-container">
                        <button class="dropdown-toggle-custom d-flex justify-content-between align-items-center" data-target="#dd1">
                            <span>Any Time</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu-custom" id="dd1">
                            <div class="item">Any Time</div>
                            <div class="item">Today</div>
                            <div class="item">Last Week</div>
                            <div class="item">Last Month</div>
                            <div class="item">Last Year</div>
                        </div>
                    </div>
                </div>

                <!-- OWNER -->
                <div class="filter-row">
                    <label>Owner</label>

                    <div class="dropdown-container">
                        <button class="dropdown-toggle-custom" data-target="#dd2">Anyone</button>

                        <div class="dropdown-menu-custom" id="dd2">
                            <div class="item">Anyone</div>
                            <div class="item">Owned by Me</div>
                            <div class="item">Not Owned by Me</div>
                        </div>
                    </div>
                </div>

                <!-- TYPE -->
                <div class="filter-row">
                    <label>Type</label>

                    <div class="dropdown-container">
                        <button class="dropdown-toggle-custom" data-target="#dd3">Any Type</button>

                        <div class="dropdown-menu-custom" id="dd3">
                            <div class="item">Document</div>
                            <div class="item">Spreadsheet</div>
                            <div class="item">PDF</div>
                        </div>
                    </div>
                </div>

                <!-- LABEL -->
                <div class="filter-row">
                    <label>Label</label>

                    <div class="dropdown-container">
                        <button class="dropdown-toggle-custom" data-target="#dd4">Any</button>

                        <div class="dropdown-menu-custom" id="dd4">
                            <div class="item">Any</div>
                            <div class="item">Lain</div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Sort Button with Dropdown -->
    <div class="sort-box mx-2 position-relative">
        <button class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center sort-toggle"
            style="width: 45px; height: 45px;" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ph ph-arrows-down-up fs-5"></i>
        </button>

        <!-- Sort Dropdown Menu -->
        <div class="dropdown-menu shadow border-0 rounded-4 p-2" style="min-width: 200px;">
            <a class="dropdown-item d-flex align-items-center gap-2 sort-option" href="#"
                data-sort="alphabetical">
                Alphabetical
            </a>
            <a class="dropdown-item d-flex align-items-center gap-2 sort-option" href="#"
                data-sort="reverse_alphabetical">
                Reverse Alphabetical
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item d-flex align-items-center gap-2 sort-option" href="#" data-sort="latest">
                Latest
            </a>
            <a class="dropdown-item d-flex align-items-center gap-2 sort-option" href="#" data-sort="oldest">
                Oldest
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item d-flex align-items-center gap-2 sort-option" href="#" data-sort="smallest">
                Smallest
            </a>
            <a class="dropdown-item d-flex align-items-center gap-2 sort-option" href="#" data-sort="largest">
                Largest
            </a>
        </div>
    </div>

    <!-- Notification -->
    <div class="notif-box mx-2">
        <button class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center"
            style="width: 45px; height: 45px;">
            <i class="ph ph-bell fs-5"></i>
        </button>
    </div>

    <!-- Profile -->
    <div class="profile-box mx-2">
        <button id="profileBtn"
            class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center p-0"
            style="width: 45px; height: 45px;">
            <img id="profilePreviewBtn" src="{{ $profile['photo'] }}" alt="pp"
                class="img-fluid rounded-circle w-100 h-100 object-fit-cover">
        </button>

        <!-- Dropdown Panel -->
        <div id="profileDropdown" class="card position-absolute border-0 shadow-sm rounded-4 p-3"
            style="min-width: 230px; right: 50px; top: 80px; display: none; z-index: 1050;">
            <div class="card border-0 shadow-sm rounded-3 p-2 mb-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle overflow-hidden me-2" style="width: 42px; height: 42px;">
                        <img id="profilePreviewDropdown" src="{{ $profile['photo'] }}" alt="pp"
                            class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div>
                        <div style="font-weight: 500; font-size:20px;" class="profile-fullname">
                            {{ $profile['fullname'] }}</div>
                        <small style="font-size: 16px">{{ $profile['email'] }}</small>
                    </div>
                </div>
            </div>
            <button class="btn account-btn w-100 text-start d-flex align-items-center gap-2" style="font-size: 16px"
                data-bs-toggle="modal" data-bs-target="#accountSettingsModal">
                <i class="ph ph-gear fs-5"></i> Account Settings
            </button>

            <!-- Account Settings Modal -->
            <div class="modal fade" id="accountSettingsModal" tabindex="-1" aria-labelledby="accountSettingsLabel"
                aria-hidden="true" data-bs-backdrop="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <form id="profileUpdateForm" action="{{ route('profile.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">

                            <div class="modal-header border-0 pb-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body px-4">
                                <h3 class="modal-title fw-semibold mb-4" id="accountSettingsLabel">Account Settings
                                </h3>
                                <div class="row align-items-center" style="font-family: Rubik">
                                    <div class="col-md-4 text-center">
                                        <img id="profilePreviewModal" src="{{ $profile['photo'] }}"
                                            class="rounded-circle mb-3 shadow-sm object-fit-cover" alt="Profile Image"
                                            width="120" height="120">

                                        <div class="text-muted small mb-2">
                                            Upload an Image <br> Max file size: 10MB
                                        </div>

                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-outline-danger rounded-3"
                                                id="deletePhotoBtn">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                            <!-- Delete Confirmation Modal -->
                                            <div class="modal fade" id="deleteConfirmationModal" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 rounded-4 shadow-lg"
                                                        style="background-color: #fff; max-width: 500px;">

                                                        <!-- Close Button (Kanan Atas) -->
                                                        <button type="button"
                                                            class="btn-close position-absolute top-0 end-0 mt-3 me-3"
                                                            data-bs-dismiss="modal" aria-label="Close"
                                                            style="z-index: 10; font-size: 1.1rem; opacity: 0.7;">
                                                        </button>

                                                        <div class="modal-body py-4 px-5">
                                                            <!-- Teks Rata Kiri -->
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

                                                            <!-- Tombol: Cancel (kiri) + Delete (kanan) -->
                                                            <div class="d-flex justify-content-between align-items-center"
                                                                style="padding-left: 1.75rem;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary rounded-4 px-4 py-2"
                                                                    data-bs-dismiss="modal"
                                                                    style="min-width: 100px; font-size: 14px; border-color: #d1d5db; color: #6b7280;">
                                                                    Cancel
                                                                </button>
                                                                <button type="button" id="confirmDeleteBtn"
                                                                    class="btn btn-danger rounded-4 px-4 py-2 text-white"
                                                                    style="min-width: 100px; font-size: 14px; background-color: #dc3545; border: none;">
                                                                    Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="file" name="photo_profile" id="photoFileInput"
                                                class="d-none" accept="image/*">

                                            <button type="button" class="btn btn-outline-secondary rounded-3"
                                                style="font-size: 14px" id="uploadPhotoBtn">
                                                Upload
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Right Section -->
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="fullname" class="form-control bg-light"
                                                style="font-size: 14px" value="{{ $profile['fullname'] }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control bg-light"
                                                style="font-size: 14px" value="{{ $profile['email'] }}" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password</label><br>
                                            <button type="button" class="btn btn-outline-secondary rounded-3"
                                                style="font-size: 14px; height: 40px;" data-bs-toggle="modal"
                                                data-bs-target="#changePasswordModal">
                                                Change Password
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 gap-3" style="font-family: Rubik;">
                                <button class="btn btn-outline-secondary rounded-3" style="font-size: 14px"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-blue rounded-3" style="font-size: 14px">Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Change Password Modal -->
            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel"
                aria-hidden="true" data-bs-backdrop="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4">

                        <form id="changePasswordForm" action="{{ route('password.update') }}" method="POST">
                            @csrf

                            <!-- Header -->
                            <div class="modal-header border-0 pb-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body px-4" style="font-family: Rubik;">
                                <h3 class="modal-title fw-semibold" id="changePasswordLabel">
                                    Change Password
                                </h3>
                                <!-- Old Password -->
                                <div class="mb-3" style="padding-left: 2rem;">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password"
                                        class="form-control bg-light rounded-3" style="height: 44px; font-size: 14px;"
                                        placeholder="Enter old password" required>
                                </div>

                                <!-- New Password -->
                                <div class="mb-3" style="padding-left: 2rem;">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password"
                                        class="form-control bg-light rounded-3" style="height: 44px; font-size: 14px;"
                                        placeholder="Enter new password" required>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="mb-3" style="padding-left: 2rem;">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation"
                                        class="form-control bg-light rounded-3" style="height: 44px; font-size: 14px;"
                                        placeholder="Confirm new password" required>
                                </div>

                                <!-- Forgot Password Link -->
                                <div class="mb-3" style="padding-left: 2rem;">
                                    <a href="{{ route('forgot') }}" class="text-decoration-none"
                                        style="color: #f97316; font-size: 14px;">
                                        Forgot Password?
                                    </a>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer border-0 gap-3" style="font-family: Rubik;">
                                <button type="button" class="btn btn-outline-secondary rounded-3"
                                    style="font-size: 14px" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" id="submitChangePassword" class="btn btn-blue rounded-3"
                                    style="font-size: 14px">
                                    Change Password
                                </button>
                            </div>
                        </form>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
                            @csrf
                        </form>

                    </div>
                </div>
            </div>
            <!-- Loading Overlay for Auto Logout -->
            <div id="autoLogoutLoader"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); backdrop-filter:blur(2px); z-index:9999; align-items:center; justify-content:center;">
                <div class="spinner-border text-light" style="width:3rem; height:3rem;"></div>
            </div>

        </div>
    </div>
</div>

<script>
// Toggle dropdown
document.querySelectorAll('.dropdown-toggle-custom').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation(); // cegah event click outside

        const menu = document.querySelector(this.dataset.target);
        const isOpen = menu.style.display === "block";

        // Tutup semua dulu
        closeAll();

        if (!isOpen) {
            menu.style.display = "block";
            this.classList.add("active"); // rotate arrow
        } else {
            menu.style.display = "none";
            this.classList.remove("active");
        }
    });
});

// Update text
document.querySelectorAll('.dropdown-menu-custom .item').forEach(item => {
    item.addEventListener('click', function() {
        const menu = this.closest('.dropdown-menu-custom');
        const btn = document.querySelector(`[data-target="#${menu.id}"]`);

        btn.querySelector("span").textContent = this.textContent;

        menu.style.display = "none";
        btn.classList.remove("active");
    });
});

// Click outside to close
document.addEventListener('click', function() {
    closeAll();
});

function closeAll() {
    document.querySelectorAll('.dropdown-menu-custom').forEach(menu => menu.style.display = "none");
    document.querySelectorAll('.dropdown-toggle-custom').forEach(btn => btn.classList.remove("active"));
}

    const filterToggle = document.querySelector('.filter-toggle');
    const filterPanel = document.getElementById('filterPanel');

    filterToggle.addEventListener('click', () => {
        const isVisible = filterPanel.style.display === 'block';
        filterPanel.style.display = isVisible ? 'none' : 'block';
    });

    // Klik di luar area -> tutup panel
    document.addEventListener('click', (e) => {
        if (!filterPanel.contains(e.target) && !filterToggle.contains(e.target)) {
            filterPanel.style.display = 'none';
        }
    });


    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    // Toggle dropdown
    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const visible = profileDropdown.style.display === 'block';
        profileDropdown.style.display = visible ? 'none' : 'block';
    });

    // Klik di luar dropdown -> tutup
    document.addEventListener('click', (e) => {
        if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
            profileDropdown.style.display = 'none';
        }
    });

    const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
    const photoFileInput = document.getElementById('photoFileInput');
    const deletePhotoBtn = document.getElementById('deletePhotoBtn');

    const profilePreviewModal = document.getElementById('profilePreviewModal');
    const profilePreviewDropdown = document.getElementById('profilePreviewDropdown');
    const profilePreviewBtn = document.getElementById('profilePreviewBtn');

    // Default photo (jika ingin kembali ke foto lama saat delete)
    const defaultPhoto = "{{ $profile['photo'] }}";

    // === FUNGSI PREVIEW GAMBAR ===
    function previewImage(file) {
        if (!file) return;

        // Validasi tipe file
        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file.');
            photoFileInput.value = '';
            return;
        }

        // Validasi ukuran (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            photoFileInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const imgData = e.target.result;

            // Update semua preview
            profilePreviewModal.src = imgData;
            profilePreviewDropdown.src = imgData;
            profilePreviewBtn.src = imgData;
        };
        reader.readAsDataURL(file);
    }

    // === EVENT: Klik Upload → buka file picker ===
    uploadPhotoBtn.addEventListener('click', () => {
        photoFileInput.click();
    });

    // === EVENT: File dipilih → preview ===
    photoFileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            previewImage(file);
        }
    });

    // === EVENT: Hapus foto → tampilkan konfirmasi ===
    deletePhotoBtn.addEventListener('click', function() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        deleteModal.show();
    });

    // === Konfirmasi hapus setelah klik "Delete" di modal ===
    document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
        try {
            const response = await fetch('/profile/delete-photo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'Accept': 'application/json'
                },
                body: new FormData() // agar Laravel mengenali CSRF
            });

            const result = await response.json();

            if (result.success) {
                const finalUrl = result.photo_url + '&_=' + Date.now();

                document.querySelectorAll(
                        '#profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                    .forEach(img => img.src = finalUrl);

                bootstrap.Modal.getInstance(
                    document.getElementById('deleteConfirmationModal')
                ).hide();

                alert('Profile photo deleted!');
            } else {
                alert('Error: ' + result.message);
            }
        } catch (err) {
            alert('Connection error. Try again.');
            console.error(err);
        }
    });


    document.getElementById('accountSettingsModal')?.addEventListener('hidden.bs.modal', function() {
        const newUrl = @json(session('new_profile_photo'));
        if (newUrl) {
            const finalUrl = newUrl + (newUrl.includes('?') ? '&' : '?') + '_=' + Date.now();
            document.querySelectorAll('#profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                .forEach(img => img.src = finalUrl);
        }
    });

    // Juga refresh saat halaman di-load (jika ada flash)
    document.addEventListener('DOMContentLoaded', function() {
        const newUrl = @json(session('new_profile_photo'));
        if (newUrl) {
            document.querySelectorAll('#profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                .forEach(img => {
                    img.src = finalUrl + '&_=' + new Date().getTime();
                });
        }
    });
    document.getElementById('profileUpdateForm').addEventListener('submit', async function(e) {
        e.preventDefault(); // Cegah submit biasa

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Disable tombol
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (result.success) {
                // Update DOM secara real-time
                document.querySelectorAll(
                        '#profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                    .forEach(img => img.src = result.photo_url + '&_=' + Date.now());

                document.querySelectorAll('.profile-fullname').forEach(el => {
                    el.textContent = result.fullname;
                });

                // Tutup modal
                bootstrap.Modal.getInstance(document.getElementById('accountSettingsModal')).hide();

                // Optional: Tampilkan toast
                alert('Profile updated successfully!');

            } else {
                alert('Error: ' + result.message);
            }

        } catch (error) {
            console.error('Update failed:', error);
            alert('Connection error. Please try again.');
        } finally {
            // Kembalikan tombol
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const btn = document.getElementById('submitChangePassword');
        const originalText = btn.innerHTML;

        // Aktifkan loading
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Processing...`;

        try {
            const response = await fetch("{{ route('password.update') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: formData
            });

            const result = await response.json();

            // Kembalikan tombol
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (result.success) {

                afterPasswordChangedSuccessfully();

            } else {
                alert(result.message || "Failed to change password");
            }

        } catch (err) {
            console.error(err);
            alert("Server error. Try again.");

            // Reset tombol setelah error
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Saat password berhasil diubah
    function afterPasswordChangedSuccessfully() {
        // Tutup modal
        const modalEl = document.getElementById('changePasswordModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        // Tampilkan loading overlay logout
        const logoutLoader = document.getElementById('autoLogoutLoader');
        logoutLoader.style.display = "flex";

        // Delay sedikit agar modal benar-benar tertutup
        setTimeout(() => {
            autoLogout();
        }, 600);
    }

    function autoLogout() {
        fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token'),
                    'Content-Type': 'application/json'
                }
            })
            .then(() => {
                // Bersihkan token
                localStorage.removeItem('token');

                // Redirect ke login
                window.location.href = "/signin";

            }).catch(() => {
                alert("Logout gagal, coba manual.");
                window.location.href = "/signin";
            });
    }
</script>
