<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
        View::composer('*', function ($view) {
            $token = Session::get('token');
            $user = null;

            if ($token) {
                $response = Http::withToken($token)->get('https://pdu-dms.my.id/api/user');
                $user = $response->json();
            }

            $view->with('user', $user);
        });

        View::composer('partials.navbar', function ($view) {
            $token = Session::get('token');

            $profile = [
                'fullname' => 'User Default',
                'email' => 'default@mail.com',
                'photo' => asset('storage/images/profile-pict.jpg'),
            ];

            if ($token) {
                // PRIORITASKAN SESSION FLASH (setelah update)
                if (Session::has('new_profile_photo')) {
                    $profile = [
                        'photo' => Session::get('new_profile_photo'),
                        'fullname' => Session::get('user.fullname', 'User'),
                        'email' => Session::get('user.email', 'user@mail.com'),
                    ];
                } else {
                    try {
                        $response = Http::withToken($token)->get('https://pdu-dms.my.id/api/user');
                        if ($response->successful()) {
                            $data = $response->json('data') ?? $response->json();

                            if (!empty($data['photo_profile_path'])) {
                                $photoUrl = 'https://pdu-dms.my.id/storage/profile_photos/' . $data['photo_profile_path'];
                                $photoUrl .= '?t=' . time(); // cache buster

                                $profile = [
                                    'fullname' => $data['fullname'] ?? 'Unknown',
                                    'email' => $data['email'] ?? 'noemail@mail.com',
                                    'photo' => $photoUrl,
                                ];

                                // Simpan ke session
                                Session::put('user', $profile);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('API user profile failed', ['error' => $e->getMessage()]);
                    }
                }
            }

            $defaultPhoto = asset('storage/images/profile-pict.jpg') . '?v=' . time();

            if (empty($data['photo_profile_path'])) {
                $profile['photo'] = $defaultPhoto;
            } else {
                $profile['photo'] = 'https://pdu-dms.my.id/storage/profile_photos/' . $data['photo_profile_path'] . '?v=' . time();
            }

            $view->with('profile', $profile);
        });
    }
}
