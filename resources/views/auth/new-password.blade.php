@extends('auth.layouts')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-3 px-4">
            <img src="/images/logo.png" alt="Logo" class="img-fluid mb-2" style="max-width: 80px">
            <h4 class="welcome-text py-3">Set Your New Password</h4>
            <p class="desc-text">
                Please enter and confirm your new password to continue.
            </p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <form method="POST" class="px-4" action="{{ route('set.new.password') }}">
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

            <button type="submit" class="btn btn-orange w-100">Save New Password</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        icon.classList.toggle("ph-eye-slash");
        icon.classList.toggle("ph-eye");
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmInput = document.getElementById('confirmPassword');
        const icon = this.querySelector('i');
        confirmInput.type = confirmInput.type === "password" ? "text" : "password";
        icon.classList.toggle("ph-eye-slash");
        icon.classList.toggle("ph-eye");
    });
</script>
@endpush
