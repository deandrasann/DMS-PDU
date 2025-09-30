<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/last-opened', [DashboardController::class, 'lastOpen'])->name('last');
Route::get('/myspace', [DashboardController::class, 'myspace'])->name('myspace');
Route::get('/shared-with-me', [DashboardController::class, 'sharedWithMe'])->name('shared');

