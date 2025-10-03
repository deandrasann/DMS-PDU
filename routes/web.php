<?php

use Illuminate\Support\Facades\Route;

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