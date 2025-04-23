<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\ExpenseController;


Route::get('/', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});

Route::apiResource('groups', GroupController::class);
Route::apiResource('expenses', ExpenseController::class);