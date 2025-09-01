<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CctvController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// CCTV API Routes
Route::prefix('cctv')->group(function () {
    Route::get('/', [CctvController::class, 'index']);
    Route::post('/', [CctvController::class, 'store']);
    Route::get('/{id}', [CctvController::class, 'show']);
    Route::put('/{id}', [CctvController::class, 'update']);
    Route::delete('/{id}', [CctvController::class, 'destroy']);
    
    // Status and monitoring endpoints
    Route::get('/status/summary', [CctvController::class, 'statusSummary']);
    Route::get('/status/buildings', [CctvController::class, 'buildingStatus']);
    Route::get('/status/rooms', [CctvController::class, 'roomStatus']);
    Route::get('/alerts', [CctvController::class, 'alerts']);
    Route::get('/performance', [CctvController::class, 'performance']);
    
    // Maintenance endpoints
    Route::post('/{id}/maintenance/start', [CctvController::class, 'startMaintenance']);
    Route::post('/{id}/maintenance/end', [CctvController::class, 'endMaintenance']);
});

// Public endpoints (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('/cctv/status', [CctvController::class, 'statusSummary']);
    Route::get('/cctv/alerts', [CctvController::class, 'alerts']);
});