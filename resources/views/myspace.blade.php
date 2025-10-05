@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@include('partials.navbar')

<div class="container-fluid ">
    <div class="d-flex flex-column flex-grow-1">
        <!-- Card Folders -->
        <div class="flex-fill d-flex flex-column bg-light shadow rounded-4 mb-4 p-3">
            <h4 class="fw-semibold mb-4">Folders</h4>
            <div class="flex-fill d-flex flex-column justify-content-center align-items-center text-center p-4">
                <div class="mb-3">
                    <i class="ph ph-folder-open" style="font-size: 80px; color: #9E9E9C;"></i>
                </div>
                <p class="text-muted mb-3" style="max-width: 400px;">
                    Create a folder to get organized
                </p>
                <a href="{{ route('myspace')}}" class="btn btn-primary btn-sm px-2 text-dark">
                    Create Folder
                </a>
            </div>
        </div>

        <!-- Card Files -->
        <div class="flex-fill d-flex flex-column bg-light shadow rounded-4 p-3">
            <h4 class="fw-semibold mb-4">Files</h4>
            <div class="flex-fill d-flex flex-column justify-content-center align-items-center text-center p-4">
                <div class="mb-3">
                    <i class="ph ph-file" style="font-size: 80px; color: #9E9E9C;"></i>
                </div>
                <p class="text-muted mb-3" style="max-width: 400px;">
                    Upload your first file to begin
                </p>
                <a href="{{ route('myspace')}}" class="btn btn-primary btn-sm px-2 text-dark">
                    Create File
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
