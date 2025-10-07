@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@include('partials.navbar')
<div class="d-flex flex-column flex-shrink-0 vh-50 p-3 bg-light shadow w-100 mb-4 rounded-4 ">
    <h4 class="fw-semibold mb-4">Last Opened Folders</h4>
        <div class="container  d-flex flex-column justify-content-center align-items-center text-center p-4">
            <div class="mb-3">
                <i class="ph ph-folder-open fs-1 text-muted" style="font-size: 80px !important; color: #9E9E9C !important"></i>
            </div>
            <p class="text-muted mb-3" style="max-width: 400px; color">
                No Folder Opened Yet
            </p>
            <a  href="{{ route('myspace')}}"class="btn btn-primary btn-sm px-2 text-dark">
                Create Folder in My Space
            </a>
        </div>

</div>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow w-100 mb-4 rounded-4">
    <h4 class="fw-semibold mb-4">Last Opened Files</h4>
        <div class="container  d-flex flex-column justify-content-center align-items-center text-center p-4">
                <div class="mb-3">
                    <i class="ph ph-file fs-1" style="font-size: 80px !important; color: #9E9E9C !important"></i>
                </div>
                <p class="text-muted mb-3" style="max-width: 400px; color">
                    No File Opened Yet
                </p>
                <a  href="{{ route('myspace')}}"class="btn btn-primary btn-sm px-2 text-dark">
                    Create File in My Space
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
