<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

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
    }
}
