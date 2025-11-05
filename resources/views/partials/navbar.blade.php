
<div class="search-container d-flex flex-row align-items-center w-100 mb-2 ">
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
        <div class="filter-panel mt-3" id="filterPanel">
            <div class="card card-body rounded-4 border-0 shadow-sm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1">Date Modified</label>
                        <select class="form-select">
                            <option>Any Time</option>
                            <option>Today</option>
                            <option>Last Week</option>
                            <option>Last Month</option>
                            <option>Last Year</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1">Owner</label>
                        <select class="form-select">
                            <option>Anyone</option>
                            <option>Owned by Me</option>
                            <option> Not Owned by Me</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1">Type</label>
                        <select class="form-select">
                            <option>Any Type</option>
                            <option>Document</option>
                            <option>Spreadsheet</option>
                            <option>PDF</option>
                        </select>
                    </div>
                </div>
            </div>
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
                        <div style="font-weight: 500; font-size:20px;">{{ $profile['fullname'] }}</div>
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
                aria-hidden="true">
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
                                <h3 class="modal-title fw-semibold mb-4" id="accountSettingsLabel">Account Settings</h3>
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
                                            <input type="text" class="form-control bg-light"
                                                style="font-size: 14px" value="{{ $profile['fullname'] }}" readonly>
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
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4">

                        <form id="changePasswordForm" action="{{ route('password.update') }}" method="POST">
                            @csrf
                            @method('PATCH')

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
                                    <label class="form-label">Old Password</label>
                                    <input type="password" name="current_password"
                                        class="form-control bg-light rounded-3" style="height: 44px; font-size: 14px;"
                                        placeholder="Enter old password" required>
                                </div>

                                <!-- New Password -->
                                <div class="mb-3" style="padding-left: 2rem;">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control bg-light rounded-3"
                                        style="height: 44px; font-size: 14px;" placeholder="Enter new password"
                                        required>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="mb-3" style="padding-left: 2rem;">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation"
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
                                <button type="submit" class="btn btn-blue rounded-3" style="font-size: 14px">
                                    Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        // Kosongkan input file
        photoFileInput.value = '';

        // Kembali ke foto default
        profilePreviewModal.src = defaultPhoto;
        profilePreviewDropdown.src = defaultPhoto;
        profilePreviewBtn.src = defaultPhoto;

        // Tutup modal konfirmasi
        bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal')).hide();
    });

    document.getElementById('accountSettingsModal')?.addEventListener('hidden.bs.modal', function () {
        const newUrl = @json(session('new_profile_photo'));
        if (newUrl) {
            document.querySelectorAll('#profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                .forEach(img => {
                    img.src = newUrl;
                });
        }
    });

    // Juga refresh saat halaman di-load (jika ada flash)
    document.addEventListener('DOMContentLoaded', function () {
        const newUrl = @json(session('new_profile_photo'));
        if (newUrl) {
            document.querySelectorAll('#profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                .forEach(img => {
                    img.src = newUrl;
                });
        }
    });
</script>
