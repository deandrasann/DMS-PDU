@extends('auth.layouts')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-sm p-4 rounded-4" style="max-width: 400px; width: 100%;">
            <div class="text-center mb-3 px-4">
                @if (session('success'))
                    <div class="alert alert-success text-center">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger text-center">{{ session('error') }}</div>
                @endif

                <img src="/images/logo.png" alt="Logo" class="img-fluid mb-2" style="max-width: 80px">
                <h4 class="welcome-text py-3">Input The Code</h4>
                <p class="desc-text">
                    We've sent a 4-digit confirmation code to your email address.
                </p>
            </div>

            <form method="POST" class="px-4" action="/new-password">
                @csrf
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph ph-keyboard icon-gray"></i>
                        </span>
                        <input type="text" name="code" class="form-control" placeholder="Code" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-orange w-100">Reset Password</button>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        <span id="resendText">
                            <span class="text-orange">Resend Code</span> in <span id="timer">00:59</span>
                        </span>
                        <a href="/resend-code" id="resendLink" class="no-underline d-none text-orange">Resend Code</a>
                    </small>
                </div>


            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let duration = 59; // detik
        const timerEl = document.getElementById('timer');
        const resendText = document.getElementById('resendText');
        const resendLink = document.getElementById('resendLink');

        function startTimer() {
            let timer = duration;
            let interval = setInterval(() => {
                let minutes = String(Math.floor(timer / 60)).padStart(2, '0');
                let seconds = String(timer % 60).padStart(2, '0');
                timerEl.textContent = `${minutes}:${seconds}`;

                if (--timer < 0) {
                    clearInterval(interval);
                    resendText.classList.add('d-none');
                    resendLink.classList.remove('d-none');
                }
            }, 1000);
        }

        document.addEventListener("DOMContentLoaded", startTimer);
    </script>
@endpush
