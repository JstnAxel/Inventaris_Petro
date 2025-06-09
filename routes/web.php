<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::redirect('/', '/login')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('dashboard');

Route::middleware(['auth', 'role:user', 'verified'])->group(function () {
    Route::get('/loan-asset', \App\Livewire\AssetLoan\Create::class)->name('loan-asset');
    Route::get('/request-stationery', \App\Livewire\StationeryRequest\Create::class)->name('request-stationery');
});


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
