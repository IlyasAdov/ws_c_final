<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\ImageGenerationController;
use App\Http\Controllers\api\ImageRecognitionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    // Chat API
    Route::prefix('chat')->group(function () {
        Route::post('/conversation', [ChatController::class, 'startConversation']);
        Route::put('/conversation/{conversationId}', [ChatController::class, 'continueConversation']);
        Route::get('/conversation/{conversationId}', [ChatController::class, 'getResponse']);
    });

    // Image Generation API
    Route::prefix('imagegeneration')->group(function () {
        Route::post('/generate', [ImageGenerationController::class, 'generate']);
        Route::get('/status/{jobId}', [ImageGenerationController::class, 'getStatus']);
        Route::get('/result/{jobId}', [ImageGenerationController::class, 'getResult']);
        Route::post('/upscale', [ImageGenerationController::class, 'upscale']);
        Route::post('/zoom/in', [ImageGenerationController::class, 'zoomIn']);
        Route::post('/zoom/out', [ImageGenerationController::class, 'zoomOut']);
    });

    // Image Recognition API
    Route::post('/imagerecognition/recognize', [ImageRecognitionController::class, 'recognize']);
});