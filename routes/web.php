<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

// Route::view('/', 'home/login');
Route::redirect('/', url('home/login'));

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

require __DIR__.'/auth.php';
