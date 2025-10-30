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
        <button class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center p-0"
            style="width: 45px; height: 45px;" data-bs-toggle="modal" data-bs-target="#accountSettingsModal">
            <img src="{{ asset('storage/images/profile-pict.jpg') }}" alt="pp"
                class="img-fluid rounded-circle w-100 h-100 object-fit-cover">
        </button>
    </div>
</div>

<!-- Account Settings Modal -->
<div class="modal fade" id="accountSettingsModal" tabindex="-1" aria-labelledby="accountSettingsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="accountSettingsLabel">Account Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="accountForm">
                    <div class="d-flex align-items-start gap-4 mb-4">
                        <div class="text-center">
                            <img id="profileImage" src="{{ asset('storage/images/profile-pict.jpg') }}"
                                class="rounded-circle border object-fit-cover" style="width:90px; height:90px;">
                            <p class="mt-2 small text-muted">Upload an Image<br>Max file size: 10MB</p>
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2">Delete</button>
                            <input type="file" id="uploadImage" hidden>
                            <button type="button" class="btn btn-light btn-sm w-100"
                                onclick="document.getElementById('uploadImage').click()">Upload</button>
                        </div>

                        <div class="flex-fill">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control" id="fullName" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="email" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <button type="button" class="btn btn-light border">Change Password</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="accountForm" class="btn btn-primary">Save Changes</button>
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

    document.addEventListener("DOMContentLoaded", () => {
        const accountModal = document.getElementById('accountSettingsModal');

        accountModal.addEventListener('show.bs.modal', async () => {
            try {
                const res = await fetch('{{ route('user.profile') }}');
                const data = await res.json();

                if (data && !data.error) {
                    document.getElementById('fullName').value = data.fullname || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('profileImage').src = data.avatar_url ||
                        '{{ asset('storage/images/profile-pict.jpg') }}';
                }
            } catch (err) {
                console.error("Gagal ambil data profil:", err);
            }
        });
    });
</script>
