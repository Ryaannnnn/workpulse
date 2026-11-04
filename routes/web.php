<?php

use App\Http\Controllers\WorkPulseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WorkPulse Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Utama / Landing Page (Blade Murni)
Route::get('/', [WorkPulseController::class, 'landing'])->name('home');

// Autentikasi Web Blade
Route::get('/login', [WorkPulseController::class, 'showLogin'])->name('login');
Route::post('/login', [WorkPulseController::class, 'handleWebLogin']);
Route::get('/register', [WorkPulseController::class, 'showRegister'])->name('register');
Route::post('/register', [WorkPulseController::class, 'handleWebRegister']);
Route::post('/logout', [WorkPulseController::class, 'handleWebLogout'])->name('logout');

// Dashboard & Asesmen Blade
Route::get('/dashboard', [WorkPulseController::class, 'showDashboard'])->name('dashboard');
Route::get('/assessment', [WorkPulseController::class, 'showAssessment'])->name('assessment');
Route::post('/assessment', [WorkPulseController::class, 'handleWebAssessment'])->name('assessment.submit');

// Endpoint kompatibilitas frontend client legacy
Route::any('/api.php', [WorkPulseController::class, 'handleAction']);
Route::any('/backend/api.php', [WorkPulseController::class, 'handleAction']);


