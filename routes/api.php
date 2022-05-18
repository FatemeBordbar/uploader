<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\UploadController;

Route::prefix('V1/')->namespace('api/V1/')->group(function () {
    Route::post('/register', [RegisterController::class, 'register'])->name('api-register');
    Route::post('/login', [LoginController::class, 'login'])->name('api-login');
    Route::middleware('auth:api')->post('/file_upload', [UploadController::class, 'fileUpload']);
});

