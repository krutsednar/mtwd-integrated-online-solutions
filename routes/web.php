<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');
Route::redirect('/', url('home/login'));
Route::redirect('/login', url('home/login'));

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
