<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\ExpenseController;

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
    return redirect()->route('expenses.index');
});

Route::resource('groups', GroupController::class);
Route::resource('expenses', ExpenseController::class);
