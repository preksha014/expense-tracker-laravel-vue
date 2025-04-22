<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ExpenseController;


Route::get('/', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});

Route::apiResource('groups', GroupController::class);
Route::apiResource('expenses', ExpenseController::class);