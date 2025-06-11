<?php

use App\Http\Middleware\CustomAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GejalaController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\QuizController;

Route::get('/', function (Request $request) {
    return response()->json([
        'title' => 'Project Akhir Pemlan',
        'mahasiswa' => [
            [
                'nama' => 'Gilang Fachrul Huda',
                'nim' => '205150207111034',
                'role' => 'Backend Developer/product manager',
            ],
            [
                'nama' => 'Rafi Ananta Nugraha',
                'nim' => '235150200111035',
                'role' => 'Frontend Developer/ui ux Designer',
            ],
            [
                'nama' => 'Rachmat Thirdi Maliki',
                'nim' => '235150200111032',
                'role' => 'Frontend Developer/product manager',
            ],
            [
                'nama' => 'M. Naufal Alfarizki',
                'nim' => '235150207111032',
                'role' => 'Frontend Developer/ui ux Designer',
            ],
        ]
    ]);
});

Route::middleware([CustomAuthMiddleware::class])->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'CustomAuthMiddleware is working']);
    });
});


   Route::controller(AuthController::class)->group(function () {
        Route::post('auth/register', 'register');
        Route::post('auth/login', 'login')->name('login'); 
        Route::post('auth/logout', 'logout');
        Route::get('auth/current-user', 'currentUser')->middleware(CustomAuthMiddleware::class); 
        Route::put('auth/change-family-role', 'changeFamilyRole');
        Route::post('auth/pair-family', 'pairFamily')->middleware(CustomAuthMiddleware::class); 
        Route::get('auth/count-parent-roles', 'countParentRoles'); 
        Route::get('auth/list-users', 'listUsers'); 
    });

Route::controller(GejalaController::class)->group(function () {
    Route::get('gejala/get-all', 'index');
    Route::post('gejala/create', 'store')->middleware(CustomAuthMiddleware::class);
    Route::put('gejala/edit', 'update')->middleware(CustomAuthMiddleware::class);
    Route::delete('gejala/delete', 'destroy')->middleware(CustomAuthMiddleware::class);
});

Route::controller(DiaryController::class)->group(function () {
    Route::get('diary/get-all', 'index')->middleware(CustomAuthMiddleware::class);
    Route::get('diary/by-user', 'getByUser')->middleware(CustomAuthMiddleware::class);
    Route::post('diary/create', 'store')->middleware(CustomAuthMiddleware::class);
    Route::put('diary/edit', 'update')->middleware(CustomAuthMiddleware::class);
    Route::delete('diary/delete', 'destroy')->middleware(CustomAuthMiddleware::class);
});

Route::controller(QuizController::class)->group(function () {
    Route::get('quizzes', 'index');
    Route::get('quizzes/user/progress', 'progress')->middleware(CustomAuthMiddleware::class); 
    Route::get('quizzes/{id}', 'show');
    Route::post('quizzes', 'store')->middleware(CustomAuthMiddleware::class);
    Route::post('quizzes/{quizId}/submit', 'submit')->middleware(CustomAuthMiddleware::class);
    Route::get('quiz-history', 'history')->middleware(CustomAuthMiddleware::class);
});
