@extends('auth.layouts')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">
            <div class="text-center mb-3 px-4">
                <img src="img/logo v1.png" alt="Logo" class="img-fluid mb-2" style="max-width: 80px">
                <h4 class="welcome-text py-3">Forgot Your Password?</h4>
                <p class="desc-text">
                    Enter the email address associated with your account, and we'll send a confirmation code to reset your
                    password.
                </p>
            </div>
            @if (session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-center">{{ session('error') }}</div>
            @endif
            <form method="POST" class="px-4"  action="{{ route('forgot.process') }}">
                @csrf
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-envelope-simple icon-gray"></i>
                        </span>
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-orange w-100">Send Code</button>
            </form>
        </div>
    </div>
@endsection
