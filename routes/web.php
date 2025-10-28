<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MySpaceController;

Route::get('/', function () {
    return redirect()->route('signin');
});
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/last-opened', [DashboardController::class, 'lastOpen'])->name('last');

Route::get('/myspace', [MySpaceController::class, 'index'])->name('myspace');
Route::get('/my-files', [MySpaceController::class, 'getFiles'])->name('my-files');
Route::get('/files/{fileId}', [MySpaceController::class, 'proxyPdf'])->name('pdf.view');
Route::get('/file-view/{fileId}', [MySpaceController::class, 'viewFile'])->name('file.view');



Route::get('/shared-with-me', [DashboardController::class, 'sharedWithMe'])->name('shared');
Route::get('/upload', [DashboardController::class, 'uploadFile'])->name('upload');


Route::get('/signin', fn() => view('auth.signin'))->name('signin');
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
