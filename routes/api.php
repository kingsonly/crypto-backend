<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

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

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/transaction', [TransactionController::class, 'index']);
    Route::get('/transaction/notify/{id}', [TransactionController::class, 'notifyAdminOfTransaction']);
    Route::get('/transaction/view/{id}', [TransactionController::class, 'show']);
    Route::post('/transaction/wallet', [TransactionController::class, 'wallet']);
    Route::post('/transaction/deposit', [TransactionController::class, 'deposit']);
    Route::get('/transaction/confirm-deposit/{id}', [TransactionController::class, 'confirmDeposit']);
    //Route::post('/demand-notice', [DemandNoticeController::class, 'index']);
});
