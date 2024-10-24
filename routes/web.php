<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TraduzioneController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/', [TraduzioneController::class, 'index']);
Route::post('/traduci', [TraduzioneController::class, 'traduci']);
Route::get('/genera-casuale', [TraduzioneController::class, 'generaCasuale']);
Route::post('/clear-results', [TraduzioneController::class, 'clearResults']);
Route::post('/set-image-size', [TraduzioneController::class, 'setImageSize']);

require __DIR__.'/auth.php';
