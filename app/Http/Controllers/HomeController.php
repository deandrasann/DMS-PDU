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
        $request->validate([
            'fullname' => 'required|string|max:255',
            'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $token = Session::get('token');
        if (!$token) {
            return redirect('/signin')->with('error', 'Authentication required.');
        }

        try {
            $http = Http::withToken($token);

            // Attach file jika ada
            if ($request->hasFile('photo_profile')) {
                $file = $request->file('photo_profile');
                $http = $http->attach(
                    'photo_profile',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }
            $http = $http->attach('fullname', $request->fullname);
            // KIRIM _method SEBAGAI FIELD MULTIPART
            $http = $http->attach('_method', 'PATCH');

            // POST tanpa payload kedua
            $response = $http->post('https://pdu-dms.my.id/api/update-profile');

            Log::info('Profile Update API', [
                'status' => $response->status(),
                'body' => $response->body(),
                'has_file' => $request->hasFile('photo_profile'),
            ]);

            if ($response->successful()) {
                $json = $response->json();
                $newPath = $json['data']['photo_profile_path'] ?? null;

                $newPhotoUrl = $newPath
                    ? 'https://pdu-dms.my.id/storage/profile_photos/' . $newPath . '?v=' . time()
                    : asset('storage/images/profile-pict.jpg') . '?v=' . time();

                $fullname = $request->fullname;

                // Simpan ke session (untuk fallback refresh)
                Session::flash('new_profile_photo', $newPhotoUrl);
                Session::put('user.fullname', $fullname);
                Session::put('user.email', Session::get('user.email')); // pastikan email tetap

                // Return JSON untuk AJAX
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'photo_url' => $newPhotoUrl,
                    'fullname' => $fullname,
                ]);
            }

            return back()->with('error', 'Update failed: ' . ($response->json('message') ?? 'Unknown error'));
        } catch (Exception $e) {
            Log::error('Profile Update Error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Connection error: ' . $e->getMessage());
        }
    }
    public function deletePhoto(Request $request)
    {
        $token = Session::get('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }

        try {
            // Request ke API backend
            $response = Http::withToken($token)->post(
                'https://pdu-dms.my.id/api/delete-photo-profile'
            );

            if ($response->successful()) {

                $defaultUrl = asset('storage/images/profile-pict.jpg') . '?v=' . time();

                // Simpan ke session agar konsisten setelah refresh
                Session::flash('new_profile_photo', $defaultUrl);

                return response()->json([
                    'success' => true,
                    'photo_url' => $defaultUrl,
                    'message' => 'Photo deleted successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json('message') ?? 'Delete failed.'
            ], 400);
        } catch (Exception $e) {
            Log::error('Delete Photo Error', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $token = Session::get('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        }

        try {
            $response = Http::withToken($token)->post('https://pdu-dms.my.id/api/change-password', [
                'current_password' => $request->current_password,
                'new_password' => $request->new_password,
                'new_password_confirmation' => $request->new_password_confirmation,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password changed successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json()['message'] ?? 'Password change failed.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
