<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/last-opened', [DashboardController::class, 'lastOpen'])->name('last');
Route::get('/myspace', [DashboardController::class, 'myspace'])->name('myspace');
Route::get('/shared-with-me', [DashboardController::class, 'sharedWithMe'])->name('shared');

Route::get('/', function () {
    return view('auth.signin');
});
Route::get('/signup', function () {
    return view('auth.signup');
});
Route::get('/forgot-password', function () {
    return view('auth.forgot');
});
Route::post('/send-code', function () {
    return view('auth.input-code');
});
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
