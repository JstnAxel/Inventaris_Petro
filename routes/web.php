<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;
use App\Models\Asset;
use App\Models\Stationary;


Route::redirect('/', '/login')->name('home');

Route::get('dashboard', function () {
    $assets = Asset::where('status', 'available')->count();
    $stationeries = Stationary::where('stock', '>', 0)->count();
    $user = Auth::user();

    return view('dashboard', compact('assets', 'stationeries', 'user'));
})->middleware(['auth', 'verified', 'role:user'])->name('dashboard');

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

Route::get('/export-stationary', \App\Http\Controllers\StationaryExportController::class)->name('export.stationary');
Route::get('/export-assets', \App\Http\Controllers\AssetExportController::class)->name('export.assets');

require __DIR__.'/auth.php';
