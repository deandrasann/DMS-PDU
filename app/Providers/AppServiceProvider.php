<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $token = Session::get('token');
            $user = null;

            if ($token) {
                $response = Http::withToken($token)->get('http://pdu-dms.my.id/api/user');
                $user = $response->json();
            }

            $view->with('user', $user);
        });

        View::composer('partials.navbar', function ($view) {
            $token = Session::get('token');

            $profile = [
                'fullname' => 'User Default',
                'email' => 'default@mail.com',
                'photo' => asset('storage/images/profile-pict.jpg'), // fallback
            ];

            if ($token) {
                try {
                    $response = Http::withToken($token)->get('http://pdu-dms.my.id/api/user');

                    if ($response->successful()) {
                        $data = $response->json('data') ?? $response->json();

                        if (is_array($data) && !empty($data['photo_profile_path'])) {
                            // Bangun URL dari path
                            $photoPath = $data['photo_profile_path'];
                            $photoUrl = 'http://pdu-dms.my.id/storage/profile_photos/' . $photoPath;

                            // Tambahkan cache buster
                            $photoUrl .= (strpos($photoUrl, '?') === false ? '?' : '&') . 't=' . time();

                            $profile = [
                                'fullname' => $data['fullname'] ?? 'Unknown User',
                                'email' => $data['email'] ?? 'noemail@mail.com',
                                'photo' => $photoUrl,
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('API user profile failed', ['error' => $e->getMessage()]);
                }
            }

            $view->with('profile', $profile);
        });
    }
}
