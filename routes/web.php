<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MySpaceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShareController;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    return redirect()->route('signin');
});

// Auth routes
Route::get('/signin', function (Request $request) {
    if ($request->has('redirect')) {
        session()->put('after_login_redirect', $request->redirect);
    }

    return view('auth.signin');
})->name('signin');

Route::post('/signin', [AuthController::class, 'login'])->name('signin.process');

Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', fn() => view('auth.forgot'))->name('forgot');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.process');
Route::get('/input-code', [AuthController::class, 'showInputCode'])->name('input.code');
Route::post('/input-code', [AuthController::class, 'verifyCode'])->name('verify.code');
Route::get('/resend-code', [AuthController::class, 'resendCode'])->name('resend.code');
Route::get('/new-password', [AuthController::class, 'showNewPassword'])->name('new.password');
Route::post('/new-password', [AuthController::class, 'setNewPassword'])->name('set.new.password');

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/last-opened', [MySpaceController::class, 'lastOpened'])->name('last');
Route::get('/shared-with-me', [DashboardController::class, 'sharedWithMe'])->name('shared');
Route::get('/upload', [DashboardController::class, 'uploadFile'])->name('upload');

// Profile routes
Route::patch('/profile/update', [HomeController::class, 'update'])->name('profile.update');
Route::post('/profile/delete-photo', [HomeController::class, 'deletePhoto'])->name('profile.delete.photo');
Route::post('/change-password', [HomeController::class, 'updatePassword'])->name('password.update');

// MySpace routes - HARUS URUT YANG SPESIFIK DULU
Route::prefix('myspace')->group(function () {
    // Route spesifik dulu
    Route::post('/upload', [MySpaceController::class, 'upload'])->name('myspace.upload');

    // Route utama
    Route::get('/', [MySpaceController::class, 'index'])->name('myspace');

    // Dynamic path TERAKHIR
    Route::get('/{path}', [MySpaceController::class, 'index'])
        ->where('path', '.*')
        ->name('myspace.subfolder');
});

// API routes - di luar group myspace
Route::get('/my-files', [MySpaceController::class, 'getFiles'])->name('my-files');
Route::get('/files/{fileId}', [MySpaceController::class, 'proxyPdf'])->name('pdf.view');
Route::get('/file-view/{fileId}', [MySpaceController::class, 'viewFile'])->name('file.view');

// Di routes/web.php
Route::middleware(['web'])->group(function () {
    Route::get('/file/{fileId}/edit', [MySpaceController::class, 'editFile'])->name('file.edit');
    Route::put('/file/{fileId}/update', [MySpaceController::class, 'updateFile'])->name('file.update');
});

Route::get('/share/{token}', ShareController::class);
