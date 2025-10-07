@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@include('partials.navbar')
{{-- <div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4 ">
    <h4 class="fw-semibold mb-4">Recomended Folders</h4>
    <!-- Grid Container -->
    <div class="row g-3">
        <!-- Folder Card -->
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 ">
            <div class="position-relative">
                <img src="{{ asset('storage/images/folder.svg') }}" alt="Folder" class="img-fluid w-100 h-100 object-fit-contain" style="min-height: 100px; min-width:120px">
                <!-- Overlay isi folder -->
                <div class="position-absolute top-0 start-0 p-2 p-sm-3 w-100 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <p class="fw-normal mt-2 mb-0 " id="folder-title">Folder Title</p>
                        <small class="fw-light" id="folder-items">19 items</small>
                        <div class="d-flex justify-content-end">
                         <button class="btn btn-link ms-auto text-dark p-0" data-bs-toggle="dropdown">
                            <i class="ph ph-dots-three-vertical fs-5 text-muted" style="cursor: pointer;"></i>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Download</a></li>
                                <li><a class="dropdown-item" href="#">Share</a></li>
                                <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                            </ul>
                        </button>
                    </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div> --}}
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
    <h4 class="fw-semibold mb-4">Recomended Files</h4>
    <!-- Grid Container -->
    <div class="row g-3 ms-1">
        <!-- File Card -->
            <div class="card rounded-4  border-dark-subtle border-1" style="width:200px; background-color:#F2F2F0;">

            <!-- Preview -->
            <div class="mt-3 mx-2">
                <canvas id="pdf-thumb" class="pdf-inner-shadow border rounded" style="border:1px solid #ddd; width:100%; max-width:300px;"></canvas>
            </div>
            <!-- Body -->
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="ph ph-file-pdf me-2"></i>
                    <span class="fw-semibold text-truncate">Judul PDF.pdf</span>
                </div>

                <!-- Labels -->
                <div class="d-flex gap-2">
                    <span class="btn-badge rounded-2 px-2"><small>Label 1</small></span>
                    <button class="btn btn-link ms-auto text-dark p-0" data-bs-toggle="dropdown">
                        <i class="ph ph-dots-three-vertical fs-5 text-muted" style="cursor: pointer;"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Download</a></li>
                        <li><a class="dropdown-item" href="#">Share</a></li>
                        <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- IS EMPTY --}}
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow  rounded-4 ">
    <h4 class="fw-semibold mb-4">Welcome to Your Dashboard</h4>
        <div class="container vh-100 d-flex flex-column justify-content-center align-items-center text-center">
            <div class="mb-3">
                <i class="ph ph-envelope-simple-open fs-1 text-muted" style="font-size: 80px !important; color: #9E9E9C !important"></i>
            </div>
            <p class="text-muted mb-3" style="max-width: 400px; color">
                Your frequently used files and folders will appear here for quick access.
                Get started by uploading your first file in "My Space".
            </p>
            <a  href="{{ route('myspace')}}"class="btn btn-primary btn-sm px-2 text-dark">
                Go to My Space
            </a>
        </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const url = "{{ asset('storage/dummypdf/UTS PMLD_v2.pdf') }}"; // URL PDF dari storage link
        const canvas = document.getElementById("pdf-thumb");
        const context = canvas.getContext("2d");

        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdf.getPage(1).then(page => {
                const viewport = page.getViewport({ scale: 1 });
                canvas.width = viewport.width;
                canvas.height = viewport.height / 2;

                page.render({
                    canvasContext: context,
                    viewport: viewport
                });
            });
        }).catch(err => {
            console.error("Gagal render PDF:", err);
            canvas.outerHTML = `<p style="color:red">PDF gagal dimuat</p>`;
        });
    });
</script>
@endsection
