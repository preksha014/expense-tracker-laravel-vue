<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\ExpenseController;
use App\Http\Controllers\api\AuthController;

Route::get('/', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('groups', GroupController::class);
    Route::get('/expenses/export-csv', [ExpenseController::class, 'exportCSV']);
    Route::get('/expenses/export-pdf', [ExpenseController::class, 'exportPDF']);
    Route::apiResource('expenses', ExpenseController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});