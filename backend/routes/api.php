<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TugasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Produk CRUD (UTS Resource - Baru & Berbeda dari tugas sebelumnya)
    Route::apiResource('produk', ProdukController::class);

    // Tugas CRUD (Backward compatibility)
    Route::get('/tugas', [TugasController::class, 'index']);
    Route::post('/tugas', [TugasController::class, 'store']);
    Route::get('/tugas/{tugas}', [TugasController::class, 'show']);
    Route::put('/tugas/{tugas}', [TugasController::class, 'update']);
    Route::delete('/tugas/{tugas}', [TugasController::class, 'destroy']);
});
