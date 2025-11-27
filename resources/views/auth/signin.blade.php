@extends('auth.layouts')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-auth">
        <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">
            <div class="text-center mb-2 px-4">
                <!-- Logo Dummy -->
                <div class="mb-3">
                    <img src="img/logo v1.png" alt="Logo" class="img-fluid" style="max-width: 80px">
                </div>
                <h4 class="welcome-text py-3">Welcome Back!</h4>
                <p class="desc-text">
                    Log in to your PT PDU Document Management System.
                </p>
            </div>

            <!-- Form Login -->
            <form method="POST" class="px-4" action="{{ route('signin.process') }}" id="loginForm">
                @csrf
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-envelope-simple icon-gray"></i>
                        </span>
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-lock icon-gray"></i>
                        </span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                            required>
                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                            <i class="ph ph-eye icon-gray"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <a href="/forgot-password" class="small text-muted no-underline">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-orange w-100" id="submitBtn">Sign In</button>

            </form>
            @if ($errors->any())
                <div class="text-danger text-center mt-2">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="text-center mt-1">
                <small class="text-muted">Didn't Have Account?
                    <a href="/signup" class="text-orange no-underline">Sign Up</a>
                </small>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                // Cegah submit ganda
                if (submitBtn.disabled) return;

                // Ganti teks + tambah spinner kecil
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Signing In...
                `;

                // Opsional: tambah kelas biar tombol tetap rapi
                submitBtn.classList.add('disabled');
            });
        }

        // Toggle password (tetap ada)
        const toggleBtn = document.getElementById('togglePassword');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('ph-eye', 'ph-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('ph-eye-slash', 'ph-eye');
                }
            });
        }
        });
    </script>
@endpush
