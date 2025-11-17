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
            <form method="POST" class="px-4" id="loginForm">
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

            const api = axios.create({
                baseURL: 'https://pdu-dm.my.id/api',
                withCredentials: true // WAJIB untuk Sanctum Cookie
            });

            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                submitBtn.disabled = true;
                submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Signing In...
        `;

                const email = document.querySelector('input[name="email"]').value;
                const password = document.querySelector('input[name="password"]').value;

                try {
                    // 1. Ambil CSRF cookie dari Sanctum
                    await api.get('/csrf-cookie');

                    // 2. Kirim login
                    await api.post('/login-user', {
                        email: email,
                        password: password
                    });

                    // 3. Redirect ke dashboard / home
                    window.location.href = "/myspace";

                } catch (error) {
                    alert("Login gagal. Pastikan email/password benar.");

                    console.error(error);

                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = "Sign In";
                }
            });
        });
    </script>
@endpush
