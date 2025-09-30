<div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow mh-100 vh-100" style="width: 280px;">
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto p-2 link-dark text-decoration-none">
       <img src="{{ asset('storage/images/logo v2.png') }}" alt="Logo" id="sidebarLogo">
    </a>
    <ul class="nav nav-pills flex-column mb-auto mt-4">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ph ph-house me-2 fs-5"></i>
                <span class="sidebar-text">Home</span>
            </a>
        </li>
        <li>
            <a href="{{ route('last') }}" class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('last') ? 'active' : '' }}">
                <i class="ph ph-clock-counter-clockwise me-2 fs-5"></i>
                <span class="sidebar-text">Last Opened</span>
            </a>
        </li>
        <li>
            <a href="{{ route('myspace') }}" class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('myspace') ? 'active' : '' }}">
                <i class="ph ph-folder-user me-2 fs-5"></i>
                <span class="sidebar-text">My Space</span>
            </a>
        </li>
        <li class="border-top border-1 border-dark my-2"></li>
        <li>
            <a href="{{ route('shared') }}" class="nav-link d-flex align-items-center text-dark {{ request()->routeIs('shared') ? 'active' : '' }}">
                <i class="ph ph-users-three me-2 fs-5"></i>
                <span class="sidebar-text">Shared With Me</span>
            </a>
        </li>
    </ul>
    <hr>
    <div>
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none ">
            <p><strong>Log Out</strong></p>
        </a>
    </div>
</div>




<!-- Bootstrap + JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
const logo = document.getElementById('sidebarLogo');
const toggleBtn = document.getElementById('toggleBtn');
const texts = document.querySelectorAll('.sidebar-text');

toggleBtn.addEventListener('click', function() {
  sidebar.classList.toggle('collapsed');

  if (sidebar.classList.contains('collapsed')) {
    // Logo kecil
    logo.src = "{{ asset('storage/images/logo v1.png') }}";
    logo.width = 40;

    // Sembunyikan text menu
    texts.forEach(t => t.style.display = "none");

    sidebar.style.width = "80px";
  } else {
    // Logo besar
    logo.src = "{{ asset('storage/images/logo v2.png') }}";
    logo.width = 120;

    // Tampilkan kembali text menu
    texts.forEach(t => t.style.display = "inline");

    sidebar.style.width = "280px";
  }
});

</script>
