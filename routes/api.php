<?php

use App\Http\Middleware\CustomAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GejalaController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\QuizController;

Route::get('/tes', function (Request $request) {
    return response()->json(['message' => 'Server is running bro']);
});

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
    Route::post('gejala/create', 'store')->middleware(CustomAuthMiddleware::class);
    Route::put('gejala/edit', 'update')->middleware(CustomAuthMiddleware::class);
    Route::delete('gejala/delete', 'destroy')->middleware(CustomAuthMiddleware::class);
});

Route::controller(DiaryController::class)->group(function () {
    Route::get('diary/get-all', 'index')->middleware(CustomAuthMiddleware::class);
    Route::post('diary/create', 'store')->middleware(CustomAuthMiddleware::class);
    Route::put('diary/edit', 'update')->middleware(CustomAuthMiddleware::class);
    Route::delete('diary/delete', 'destroy')->middleware(CustomAuthMiddleware::class);
});

Route::controller(QuizController::class)->group(function () {
    Route::get('quizzes', 'index');
    Route::get('quizzes/{id}', 'show');
    Route::post('quizzes', 'store')->middleware(CustomAuthMiddleware::class);
    Route::post('quizzes/{quizId}/submit', 'submit')->middleware(CustomAuthMiddleware::class);
    Route::get('quiz-history', 'history')->middleware(CustomAuthMiddleware::class);
});
