@extends('auth.layouts')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-auth">
        <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">
            <div class="text-center mb-2 px-4">
                <!-- Logo Dummy -->
                <div class="mb-3">
                    <img src="img/logo v1.png" alt="Logo" class="img-fluid" style="max-width: 80px">
                </div>
                <h4 class="welcome-text py-3">Create Your Account</h4>
                <p class="desc-text">
                    Start managing your documents efficiently and securely.
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <!-- Form Login -->
            <form method="POST" class="px-4" action="{{ route('register') }}" id="registerForm">
                @csrf
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-user icon-gray"></i>
                        </span>
                        <input 
                            type="text" 
                            name="fullname" 
                            class="form-control @error('fullname') is-invalid @enderror" 
                            placeholder="Fullname" 
                            value="{{ old('fullname') }}" 
                            required
                        >
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-envelope-simple icon-gray"></i>
                        </span>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            placeholder="Email" 
                            value="{{ old('email') }}" 
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-lock icon-gray"></i>
                        </span>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                            required>
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
                            placeholder="Password Confirmation" required>
                        <span class="input-group-text" id="toggleConfirmPassword" style="cursor: pointer;">
                            <i class="ph ph-eye icon-gray"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-orange w-100" id="submitBtn">Sign Up</button>
                @if ($errors->any() || session('error'))
                    <div class="text-center mb-3">
                        <small class="text-danger">
                            {{ session('error') ?? $errors->first() }}
                        </small>
                    </div>
                @endif
            </form>

            <div class="text-center mt-2">
                <small class="text-muted">Already Have an Account?
                    <a href="/signin" class="text-orange no-underline">Sign In</a>
                </small>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                // Cegah submit ganda
                if (submitBtn.disabled) return;

                // Ganti teks + spinner kecil (SAMA PERSIS seperti Sign In)
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Signing Up...
                `;

                // Tambah kelas disabled biar tombol kelihatan "non-aktif"
                submitBtn.classList.add('disabled');
            });
        }

        // Toggle password visibility (sama seperti sebelumnya)
        document.getElementById('togglePassword')?.addEventListener('click', function() {
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

        document.getElementById('toggleConfirmPassword')?.addEventListener('click', function() {
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
    });
    </script>
@endpush
