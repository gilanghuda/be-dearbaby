<?php

use App\Http\Middleware\CustomAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GejalaController;
use App\Http\Controllers\DiaryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(CustomAuthMiddleware::class);

Route::middleware([CustomAuthMiddleware::class])->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'CustomAuthMiddleware is working']);
    });
});


   Route::controller(AuthController::class)->group(function () {
        Route::post('auth/register', 'register');;
        Route::post('auth/login', 'login')->name('login'); 
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

Route::controller(DiaryController::class)->group(function () {
    Route::get('diary/get-all', 'index');
    Route::post('diary/create', 'store')->middleware(CustomAuthMiddleware::class);
    Route::put('diary/edit', 'update');
    Route::delete('diary/delete', 'destroy');
});
