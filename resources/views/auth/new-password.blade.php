@extends('auth.layouts')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-3 px-4">
            <img src="img/logo v1.png" alt="Logo" class="img-fluid mb-2" style="max-width: 80px">
            <h4 class="welcome-text py-3">Set Your New Password</h4>
            <p class="desc-text">
                Please enter and confirm your new password to continue.
            </p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <form method="POST" class="px-4" action="{{ route('set.new.password') }}" id="resetForm">
            @csrf
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ph ph-lock icon-gray"></i>
                    </span>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="New Password" required>
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="ph ph-eye icon-gray"></i>
                    </span>
                </div>
            </div>
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ph ph-lock icon-gray"></i>
                    </span>
                    <input type="password" id="confirmPassword" name="password_confirmation" class="form-control"
                        placeholder="Confirm Password" required>
                    <span class="input-group-text" id="toggleConfirmPassword" style="cursor: pointer;">
                        <i class="ph ph-eye icon-gray"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-orange w-100" id="submitBtn">Save New Password</button>
        </form>
    </div>

    {{-- FULL SCREEN LOADING OVERLAY + BLUR BACKGROUND --}}
    <div id="fullScreenLoader" class="position-fixed top-0 start-0 w-100 h-100 d-none"
         style="background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 9999;">
        <div class="d-flex flex-column justify-content-center align-items-center h-100">
            <div class="spinner-border text-orange mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="text-orange fw-semibold">Saving your new password...</h5>
            <small class="text-muted">Please wait a moment</small>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');
        const loader = document.getElementById('fullScreenLoader');

        // Toggle password visibility
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
            }
        });

        document.getElementById('toggleConfirmPassword')?.addEventListener('click', function () {
            const input = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
            }
        });

        // FULL SCREEN LOADING + BLUR + DISABLE BUTTON
        form.addEventListener('submit', function () {
            // Disable tombol
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Saving...
            `;

            // Tampilkan full screen loader dengan blur
            loader.classList.remove('d-none');
        });
    });
</script>
@endpush
