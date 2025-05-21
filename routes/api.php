<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GejalaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


   Route::controller(AuthController::class)->group(function () {
        Route::post('auth/register', 'register');;
        Route::post('auth/login', 'login');;
        Route::post('auth/logout', 'logout');
        Route::get('auth/current-user', 'currentUser');
        Route::put('auth/change-family-role', 'changeFamilyRole');
    });

Route::controller(GejalaController::class)->group(function () {
    Route::get('gejala/get-all', 'index');
    Route::post('gejala/create', 'store')->middleware('role:admin');
    Route::put('gejala/edit', 'update')->middleware('role:admin');
    Route::delete('gejala/delete', 'destroy')->middleware('role:admin');
});
