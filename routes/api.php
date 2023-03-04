<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth', [AuthController::class,'login'])->name('auth');
Route::post('/logout',[AuthController::class,'logout'])->name('logout');

Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('/lead/{id}', [LeadController::class,'show'])->name('leads.show');
    Route::post('/lead', [LeadController::class,'store'])->name('leads.store');
    Route::get('/leads', [LeadController::class,'index'])->name('leads.index');
});
