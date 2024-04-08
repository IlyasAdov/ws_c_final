<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\BillingQuotaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('workspaces');
});

Route::middleware('guest')->get('/login', [AuthController::class, 'signin'])->name('login');
Route::middleware('guest')->post('/login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->prefix('workspaces')->group(function () {
    // Workspace routes
    Route::get('/', [WorkspaceController::class, 'index'])->name('workspaces');
    Route::get('/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/create', [WorkspaceController::class, 'store']);
    
    Route::middleware(['validWorkspace'])->group(function () {
        Route::get('/{workspaceId}', [WorkspaceController::class, 'show'])->name('workspaces.show');
        Route::get('/{workspaceId}/edit', [WorkspaceController::class, 'edit'])->name('workspaces.edit');
        Route::post('/{workspaceId}/edit', [WorkspaceController::class, 'update'])->name('workspaces.update');

        // API Token routes
        Route::get('/{workspaceId}/token/create', [ApiTokenController::class, 'create'])->name('token.create');
        Route::post('/{workspaceId}/token/create', [ApiTokenController::class, 'store']);
        Route::post('/{workspaceId}/token/{tokenId}/revoke', [ApiTokenController::class, 'destroy'])->name('token.revoked');

        // Billing Quota routes 
        Route::get('/{workspaceId}/quota', [BillingQuotaController::class, 'edit'])->name('quota.set');
        Route::post('/{workspaceId}/quota', [BillingQuotaController::class, 'update']);

        // Bill routes
        Route::get('/{workspaceId}/bills/{year}/{month}', [BillController::class, 'show'])->name('bills');
    });
});

Route::get('/files/{fileName}', [FileController::class, 'download']);