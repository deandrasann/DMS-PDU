<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Update profile (khususnya foto profil)
     */
    public function update(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ]);

        $token = Session::get('token');

        if (!$token) {
            return redirect('/signin')->with('error', 'Authentication required. Please sign in again.');
        }

        try {
            // 2. Gunakan POST + _method=PATCH + multipart/form-data
            $payload = [
                '_method' => 'PATCH', // Spoofing PATCH
            ];

            $http = Http::withToken($token)->asForm(); // asForm() = multipart/form-data

            if ($request->hasFile('photo_profile')) {
                $file = $request->file('photo_profile');
                $payload['photo_profile'] = $file; // Laravel Http akan otomatis attach file
            }

            // 3. Kirim ke API
            $response = $http->post('http://pdu-dms.my.id/api/update-profile', $payload);

            // Log untuk debugging
            Log::info('Profile Update API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'has_file' => $request->hasFile('photo_profile'),
            ]);

            // 4. Cek jika sukses
            if ($response->successful()) {
                $json = $response->json();

                // Ambil path foto baru dari respons API
                $newPath = $json['data']['photo_profile_path'] ?? null;

                if ($newPath) {
                    // Bangun URL lengkap dengan cache buster
                    $newPhotoUrl = 'http://pdu-dms.my.id/storage/profile_photos/' . $newPath;

                    // Update session agar navbar langsung refresh
                    $user = Session::get('user', []);
                    $user['photo'] = $newPhotoUrl;
                    Session::put('user', $user);

                    // Kirim URL ke frontend via session (opsional, untuk JS)
                    Session::flash('new_profile_photo', $newPhotoUrl);
                }

                return back()->with('success', 'Profile updated successfully!');
            }

            // Jika API gagal (status 4xx/5xx)
            $errorMessage = $response->json('message') ?? 'Update failed.';
            return back()->with('error', 'Update failed: ' . $errorMessage)->withInput();
        } catch (Exception $e) {
            Log::error('Profile Update Exception', ['message' => $e->getMessage()]);
            return back()->with('error', 'Connection error: ' . $e->getMessage());
        }
    }

    // public function updatePassword(Request $request)
    // {
    //     $request->validate([
    //         'current_password' => 'required',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     $token = Session::get('token');
    //     if (!$token) {
    //         return redirect('/signin')->with('error', 'Please sign in again.');
    //     }

    //     try {
    //         $response = Http::withToken($token)->asForm()->post('http://pdu-dms.my.id/api/change-password', [
    //             '_method' => 'PATCH',
    //             'current_password' => $request->current_password,
    //             'password' => $request->password,
    //             'password_confirmation' => $request->password_confirmation,
    //         ]);

    //         if ($response->successful()) {
    //             return back()->with('success', 'Password changed successfully!');
    //         }

    //         $error = $response->json('message') ?? 'Password change failed.';
    //         return back()->with('error', $error)->withInput();
    //     } catch (Exception $e) {
    //         return back()->with('error', 'Error: ' . $e->getMessage());
    //     }
    // }
}
