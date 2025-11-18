<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    // Menampilkan halaman sign up
    public function showSignup()
    {
        return view('auth.signup');
    }

    // Proses register ke API eksternal
    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Kirim data ke API eksternal
            $response = Http::post('https://pdu-dms.my.id/api/register-user', [
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);

            // Jika sukses (status code 200/201)
            if ($response->successful()) {
                return redirect('/signin')->with('success', 'Registration successful! Please sign in.');
            }

            // Jika gagal (misal validasi)
            return back()->with('error', 'Registration failed. Please check your input.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error connecting to API: ' . $e->getMessage());
        }
    }

    public function login(Request $request)
{
    $response = Http::post('https://pdu-dms.my.id/api/login-user', [
        'email' => $request->email,
        'password' => $request->password,
    ]);

    if ($response->successful()) {
        $data = $response->json();

        // Ambil token sesuai key yang benar
        $token = $data['access_token'] ?? null;

        if (!$token) {
            return back()->withErrors(['login' => 'Token tidak ditemukan dalam respons API.']);
        }

        // ✅ Simpan token secara permanen ke session
        session()->put('token', $token);

        // (Opsional) simpan juga nama user biar mudah diakses di view
        if (isset($data['user'])) {
            session()->put('user', $data['user']);
        }

        // ✅ Redirect langsung tanpa flash data
        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials or server error.',
    ]);
}



    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Kirim request ke API eksternal
            $response = Http::post('https://pdu-dms.my.id/api/forgot-password', [
                'email' => $request->email,
            ]);

            if ($response->successful()) {
                session(['reset_email' => $request->email]);
                return redirect('/input-code')->with('success', 'Verification code has been sent to your email.');
            } else {
                // Jika gagal
                return back()->with('error', 'Failed to send verification code. Please check your email address.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error connecting to API: ' . $e->getMessage());
        }
    }

    public function showInputCode()
    {
        return view('auth.input-code');
    }

    public function resendCode()
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Email tidak ditemukan, silakan isi ulang.');
        }

        // Kirim ulang kode ke API eksternal
        $response = Http::post('https://pdu-dms.my.id/api/forgot-password', [
            'email' => $email,
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Kode verifikasi telah dikirim ulang ke email Anda.');
        } else {
            return back()->with('error', 'Gagal mengirim ulang kode. Silakan coba lagi.');
        }
    }
    public function verifyCode(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        $email = session('reset_email');

        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Session expired. Please try again.');
        }

        try {
            $response = Http::post('https://pdu-dms.my.id/api/verify-token', [
                'email' => $email,
                'token' => $request->token,
            ]);

            if ($response->successful()) {
                session(['reset_token' => $request->token]);
                return redirect('/new-password')->with('success', 'Token verified! Please set your new password.');
            }

            return back()->with('error', 'Invalid verification code.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error connecting to API: ' . $e->getMessage());
        }
    }
    public function showNewPassword()
    {
        if (!session('reset_email')) {
            return redirect('/forgot-password')->with('error', 'Session expired. Please try again.');
        }

        return view('auth.new-password');
    }

    public function setNewPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $email = session('reset_email');
        $token = session('reset_token');
        if (!$email) {
            return redirect('/forgot-password')->with('error', 'Session expired. Please start the process again.');
        }

        try {
            $response = Http::post('https://pdu-dms.my.id/api/reset-password', [
                'email' => $email,
                'token' => $token,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);

            if ($response->successful()) {
                session()->forget(['reset_email', 'reset_token']);
                return redirect('/signin')->with('success', 'Password reset successfully. Please log in.');
            }

            return back()->with('error', 'Failed to reset password. Please try again.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error connecting to API: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            // Ambil token dari session
            $token = session('token');

            if (!$token) {
                return redirect('/signin')->with('error', 'You are not logged in.');
            }

            // Kirim request ke API eksternal
            $response = Http::withToken($token)->post('https://pdu-dms.my.id/api/logout-user');

            // Hapus token dari session
            session()->forget('token');

            // Jika logout sukses
            if ($response->successful()) {
                return redirect('/signin')->with('success', 'Logged out successfully.');
            }

            // Jika API gagal tapi tetap hapus session
            return redirect('/signin')->with('warning', 'Logout locally, but API failed.');
        } catch (\Exception $e) {
            session()->forget('token');
            return redirect('/signin')->with('error', 'Error during logout: ' . $e->getMessage());
        }
    }
}
