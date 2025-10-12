@extends('auth.layouts')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-auth">
        <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">
            <div class="text-center mb-2 px-4">
                <!-- Logo Dummy -->
                <div class="mb-3">
                    <img src="/images/logo.png" alt="Logo" class="img-fluid" style="max-width: 80px">
                </div>
                <h4 class="welcome-text py-3">Create Your Account</h4>
                <p class="desc-text">
                    Start managing your documents efficiently and securely.
                </p>
            </div>

            <!-- Form Login -->
            <form method="POST" class="px-4" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-user icon-gray"></i>
                        </span>
                        <input type="fullname" name="fullname" class="form-control" placeholder="Fullname" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-envelope-simple icon-gray"></i>
                        </span>
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>

                <div class="mb-3">
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

                <button type="submit" class="btn btn-orange w-100">Sign Up</button>
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
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("ph-eye");
                icon.classList.add("ph-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("ph-eye-slash");
                icon.classList.add("ph-eye");
            }
        });
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("ph-eye");
                icon.classList.add("ph-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("ph-eye-slash");
                icon.classList.add("ph-eye");
            }
        });
    </script>
@endpush
