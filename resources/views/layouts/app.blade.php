<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMS PDU')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/css/icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/x-icon">
</head>

@if (session('token'))
    <script>
        localStorage.setItem('token', '{{ session('token') }}');
    </script>
@endif

<body class="d-flex vh-100 overflow-hidden">

    <!-- Sidebar Desktop -->
    <div class="position-fixed h-100 z-3 d-none d-lg-block">
        @include('partials.sidebar')
    </div>

    <!-- Navbar Mobile -->
    <div class="d-block d-lg-none w-100 position-fixed top-0 start-0 z-3">
        @include('partials.navbar-mobile')
    </div>

    <!-- Konten -->
    <div
        class="d-flex flex-column flex-grow-1 sidebar-collapse-content h-100 overflow-auto w-100 content-wrapper p-4 mt-lg-0 mt-5">
        {{-- Navbar desktop muncul di atas konten --}}
        <div class="d-none d-lg-block mb-3">
            @include('partials.navbar')
        </div>
        <div class="content-wrapper">
            @yield('content')
        </div>

    </div>



    <!-- JS -->

    <body class="d-flex vh-100 overflow-hidden">
        {{-- <div class="position-fixed h-100 z-3">
        @include('partials.sidebar')
    </div> --}}


        {{-- <div class="d-flex flex-column flex-grow-1 sidebar-collapse-content h-100 overflow-auto p-4">
        @yield('content')
    </div> --}}
        <!--Icons-->
        <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const $ = (s) => document.querySelector(s);
                const $$ = (s) => document.querySelectorAll(s);

                function syncProfilePhotosOnLoad() {
                    // Ambil foto dari desktop sebagai referensi
                    const desktopPhoto = $('#profilePreviewBtn, #profilePreviewDropdown');
                    const mobilePhoto = $('#mobileProfilePhoto');
                    const modalPhoto = $('#modalProfilePhoto');

                    if (desktopPhoto && mobilePhoto && desktopPhoto.src) {
                        // Sync mobile dengan desktop
                        mobilePhoto.src = desktopPhoto.src;
                        if (modalPhoto) modalPhoto.src = desktopPhoto.src;
                    }
                }

                // Jalankan sync saat load
                syncProfilePhotosOnLoad();
                // ===================================================================
                // 1. DESKTOP ONLY: Dropdown, Filter, Profile Dropdown
                // ===================================================================
                function closeAllDesktop() {
                    $$('.desktop-only .dropdown-menu-custom, .desktop-only #filterPanel, .desktop-only #profileDropdown')
                        .forEach(el => el && (el.style.display = 'none'));
                    $$('.desktop-only .dropdown-toggle-custom').forEach(btn => btn.classList.remove('active'));
                }

                // Dropdown biasa (sort, filter, dll)
                $$('.desktop-only .dropdown-toggle-custom').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        const menu = $(btn.dataset.target);
                        const isOpen = menu?.style.display === 'block';
                        closeAllDesktop();
                        if (!isOpen && menu) {
                            menu.style.display = 'block';
                            btn.classList.add('active');
                        }
                    });
                });

                // Profile dropdown desktop
                $('.desktop-only #profileBtn')?.addEventListener('click', e => {
                    e.stopPropagation();
                    const dd = $('.desktop-only #profileDropdown');
                    if (dd) dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
                });

                // Filter panel desktop
                $('.desktop-only .filter-toggle')?.addEventListener('click', e => {
                    e.stopPropagation();
                    const panel = $('.desktop-only #filterPanel');
                    if (panel) panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
                });

                // Klik di luar → hanya nutup desktop
                document.addEventListener('click', e => {
                    if (!e.target.closest('.desktop-only')) closeAllDesktop();
                });

                // ===================================================================
                // 2. MOBILE & DESKTOP: Upload Foto (satu fungsi untuk semua!)
                // ===================================================================
                function updateAllProfilePhotos(src) {
                    $$('#mobileProfilePhoto, #modalProfilePhoto, #profilePreviewBtn, #profilePreviewDropdown, #profilePreviewModal')
                        .forEach(img => img && (img.src = src));
                }

                // Upload dari mobile
                $('.mobile-only #uploadPhotoBtn')?.addEventListener('click', () => $('.mobile-only #photoInput')
                ?.click());
                $('.mobile-only #photoInput')?.addEventListener('change', function() {
                    if (this.files?.[0]) {
                        const reader = new FileReader();
                        reader.onload = e => updateAllProfilePhotos(e.target.result);
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // Upload dari desktop
                $('.desktop-only #uploadPhotoBtn')?.addEventListener('click', () => $('.desktop-only #photoFileInput')
                    ?.click());
                $('.desktop-only #photoFileInput')?.addEventListener('change', function() {
                    if (this.files?.[0]) {
                        const reader = new FileReader();
                        reader.onload = e => updateAllProfilePhotos(e.target.result);
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // ===================================================================
                // 3. DELETE FOTO (mobile + desktop)
                // ===================================================================
                $$('.mobile-only #deletePhotoBtn, .desktop-only #deletePhotoBtn').forEach(btn => {
                    btn?.addEventListener('click', async () => {
                        if (!confirm('Hapus foto profil?')) return;

                        try {
                            const {
                                data
                            } = await axios.post('{{ route('profile.delete.photo') }}');
                            const url = data.photo_url + '?t=' + Date.now();
                            updateAllProfilePhotos(url);
                            alert('Foto dihapus!');
                        } catch (err) {
                            alert('Gagal hapus foto');
                        }
                    });
                });

                // Konfirmasi delete desktop
                $('.desktop-only #confirmDeleteBtn')?.addEventListener('click', async () => {
                    try {
                        const {
                            data
                        } = await axios.post('{{ route('profile.delete.photo') }}');
                        updateAllProfilePhotos(data.photo_url + '?t=' + Date.now());
                        bootstrap.Modal.getInstance($('.desktop-only #deleteConfirmationModal'))?.hide();
                        alert('Foto dihapus!');
                    } catch (err) {
                        alert('Gagal');
                    }
                });

                // ===================================================================
                // 4. UPDATE PROFILE (mobile + desktop)
                // ===================================================================
                $$('#profileUpdateForm').forEach(form => {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const btn = this.querySelector('button[type="submit"]');
                        const orig = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

                        try {
                            const {
                                data
                            } = await axios.post(this.action, new FormData(this));
                            const url = (data.photo_url || data.photo_path) + '?t=' + Date.now();
                            updateAllProfilePhotos(url);
                            $$('.mobile-only #mobileProfileName, .desktop-only .profile-fullname')
                                .forEach(el => el.textContent = data.fullname || data.name);

                            // Tutup modal sesuai versi
                            const modalId = this.closest('.modal')?.id;
                            if (modalId) bootstrap.Modal.getInstance(document.getElementById(
                                modalId))?.hide();

                            alert('Profil diperbarui!');
                        } catch (err) {
                            alert(err.response?.data?.message || 'Gagal update');
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = orig;
                        }
                    });
                });

                // ===================================================================
                // 5. CHANGE PASSWORD (mobile + desktop)
                // ===================================================================
                $$('#changePasswordForm').forEach(form => {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const btn = this.querySelector('button[type="submit"]') || $(
                            '.desktop-only #submitChangePassword');
                        const orig = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = 'Processing...';

                        try {
                            await axios.post('{{ route('password.update') }}', new FormData(this));
                            alert('Password diubah! Keluar otomatis...');
                            setTimeout(() => location.href = '/signin', 1500);
                        } catch (err) {
                            alert(err.response?.data?.message || 'Gagal ubah password');
                        } finally {
                            btn.disabled = false;
                            btn.innerHTML = orig;
                        }
                    });
                });

                // ===================================================================
                // 6. TOGGLE EYE PASSWORD
                // ===================================================================
                $$('.toggle-password').forEach(icon => {
                    icon.onclick = function() {
                        const input = document.querySelector(`input[name="${this.dataset.target}"]`);
                        if (!input) return;
                        if (input.type === 'password') {
                            input.type = 'text';
                            this.classList.replace('ph-eye-slash', 'ph-eye');
                        } else {
                            input.type = 'password';
                            this.classList.replace('ph-eye', 'ph-eye-slash');
                        }
                    };
                });
            });
        </script>

    </body>

</html>
