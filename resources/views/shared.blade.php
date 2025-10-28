@extends('layouts.app')

@section('title', 'Shared with Me')

@section('content')
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light shadow  rounded-2 ">
    <h4 class="fw-semibold mb-4">Shared with Me</h4>
        <div class="container vh-100 d-flex flex-column justify-content-center align-items-center text-center">
            <div class="mb-3">
                <i class="ph ph-envelope-simple-open fs-1 text-muted" style="font-size: 80px !important; color: #9E9E9C !important"></i>
            </div>
            <h5>Nothing Has Been Shared With You Yet</h5>
            <p class="text-muted mb-3" style="max-width: 400px; color">
                When a colleague shares a file or folder with you, it will appear in this section
            </p>
            <a  href="{{ route('myspace')}}"class="btn btn-primary btn-sm px-2 text-dark">
                Go to My Space
            </a>
        </div>
</div>



@endsection
