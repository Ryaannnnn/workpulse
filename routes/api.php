<?php

use App\Http\Controllers\WorkPulseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WorkPulse API Routes
|--------------------------------------------------------------------------
| Clinical-grade work culture and burnout risk assessment endpoints.
*/

Route::prefix('v1')->group(function () {
    // Autentikasi
    Route::post('/register', [WorkPulseController::class, 'register']);
    Route::post('/login', [WorkPulseController::class, 'login']);

    // Kuesioner & Indikator
    Route::get('/questions', [WorkPulseController::class, 'getQuestions']);
    Route::post('/submit', [WorkPulseController::class, 'submitAssessment']);

    // Dashboard & Agregasi
    Route::get('/dashboard', [WorkPulseController::class, 'getDashboard']);
    Route::get('/startup_list', [WorkPulseController::class, 'getStartupList']);
    Route::get('/factor_averages', [WorkPulseController::class, 'getFactorAverages']);
    Route::any('/action', [WorkPulseController::class, 'handleAction']);
});

Route::any('/api.php', [WorkPulseController::class, 'handleAction']);
Route::any('/backend/api.php', [WorkPulseController::class, 'handleAction']);


