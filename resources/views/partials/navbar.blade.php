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
    <button class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
      <i class="ph ph-bell fs-5"></i>
    </button>
  </div>

  <!-- Profile -->
  <div class="profile-box mx-2">
    <button class="btn btn-light rounded-circle shadow d-flex align-items-center justify-content-center p-0" style="width: 45px; height: 45px;">
      <img src="{{ asset('storage/images/profile-pict.jpg') }}" alt="pp" class="img-fluid rounded-circle w-100 h-100 object-fit-cover">
    </button>
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
</script>
