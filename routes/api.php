<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\ValidateController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['custom.auth'])->group(function () {
    Route::get('/challenge', [ChallengeController::class, 'getChallenge']);
    Route::get('/dumps/{dump_type}', [ChallengeController::class, 'getDumps']);
    Route::post('/validate', [ValidateController::class, 'validateChallenge']);
});