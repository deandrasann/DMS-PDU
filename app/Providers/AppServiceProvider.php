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
                $response = Http::withToken($token)->get('https://dms-pdu-api.up.railway.app/api/user');
                $user = $response->json();
            }

            // if ($token) {
            //     $response = Http::withToken($token)->get('http://127.0.0.1:8000/api/user');
            //     $user = $response->json();
            // }

            $view->with('user', $user);
        });

        View::composer('partials.navbar', function ($view) {
            $token = Session::get('token');
            $r2BaseUrl = rtrim(env('R2_URL'), '/');

            $profile = [
                'fullname' => 'User Default',
                'email' => 'default@mail.com',
                'photo' => asset('images/profile-pict.jpg'),
            ];

            if ($token) {
                if (Session::has('new_profile_photo')) {
                    $profile = [
                        'photo' => Session::get('new_profile_photo'),
                        'fullname' => Session::get('user.fullname', 'User'),
                        'email' => Session::get('user.email', 'user@mail.com'),
                    ];
                } else {
                    try {
                        $response = Http::withToken($token)->get('https://dms-pdu-api.up.railway.app/api/user');
                        if ($response->successful()) {
                            $resJson = $response->json();
                            $data = $resJson['data'] ?? $resJson;

                            // Pastikan data tidak kosong
                            if ($data) {
                                $photoUrl = !empty($data['photo_profile_path'])
                                    ? $r2BaseUrl . '/profile_photos/' . $data['photo_profile_path'] . '?t=' . time()
                                    : asset('images/profile-pict.jpg');

                                $profile = [
                                    'fullname' => $data['fullname'] ?? 'Unknown',
                                    'email' => $data['email'] ?? 'noemail@mail.com',
                                    'photo' => $photoUrl,
                                ];

                                Session::put('user', $profile);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('API user profile failed', ['error' => $e->getMessage()]);
                    }
                }
            }

            $view->with('profile', $profile);
        });
    }
}
