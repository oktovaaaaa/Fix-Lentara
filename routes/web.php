<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IslandController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NusantaraChatController;

use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TestimonialReportController;

use App\Http\Controllers\Admin\IslandStatController;
use App\Http\Controllers\Admin\IslandHistoryController; // (kalau kamu pakai, tetap aman ada)
use App\Http\Controllers\Admin\HistoryController;       // kamu pakai di resource histories

use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\TestimonialReportController as AdminTestimonialReportController;

use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuizQuestionController as AdminQuizQuestionController;


/*
|--------------------------------------------------------------------------
| NUSANTARA AI (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('/nusantara-ai/chat', [NusantaraChatController::class, 'chat'])
    ->name('nusantara.chat');

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (USER)
|--------------------------------------------------------------------------
*/

// Halaman utama: Budaya Indonesia + carousel pulau
Route::get('/', [IslandController::class, 'landing'])->name('home');

// Halaman detail tiap pulau (Sumatera, Jawa, dll)
Route::get('/islands/{island:slug}', [IslandController::class, 'show'])
    ->name('islands.show');

/*
|--------------------------------------------------------------------------
| PUBLIC: TESTIMONI + REPORT (ANTI-SPAM via throttle:testimonials)
|--------------------------------------------------------------------------
*/

Route::post('/testimoni', [TestimonialController::class, 'store'])
    ->middleware('throttle:testimonials')
    ->name('testimonials.store');

Route::patch('/testimoni/{testimonial}', [TestimonialController::class, 'update'])
    ->middleware('throttle:testimonials')
    ->name('testimonials.update');

Route::post('/testimoni/{testimonial}/report', [TestimonialReportController::class, 'store'])
    ->middleware('throttle:testimonials')
    ->name('testimonials.report');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN / LOGOUT ADMIN)
|--------------------------------------------------------------------------
*/

// Form login admin
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

// Proses login (email + password)
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN AREA (BUTUH LOGIN)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | DASHBOARD
        |----------------------------------------------------------------------
        */
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | HISTORIES (KAMU SUDAH PUNYA)
        |----------------------------------------------------------------------
        */
        Route::resource('histories', HistoryController::class)
            ->names('histories')
            ->except(['show']);

        /*
        |----------------------------------------------------------------------
        | STATS (KAMU SUDAH PUNYA)
        |----------------------------------------------------------------------
        */
        Route::resource('stats', IslandStatController::class)
            ->names('stats');

        Route::post('stats/population/{island}', [IslandStatController::class, 'updatePopulation'])
            ->name('stats.population.update');

        Route::post('stats/{island}/demographics', [IslandStatController::class, 'storeDemographic'])
            ->name('stats.demographics.store');

        Route::delete('stats/{island}/demographics/{demographic}', [IslandStatController::class, 'destroyDemographic'])
            ->name('stats.demographics.destroy');

        /*
        |----------------------------------------------------------------------
        | ADMIN: TESTIMONI & REPORT (BARU - SESUAI PERMINTAANMU)
        |----------------------------------------------------------------------
        | - /admin/testimonials           -> table testimoni (filter, reported, rating)
        | - /admin/testimonials/{id}      -> delete testimoni
        |
        | - /admin/testimonial-reports    -> table report
        | - /admin/testimonial-reports/{id} -> delete report
        */
        Route::resource('testimonials', AdminTestimonialController::class)
            ->only(['index', 'destroy'])
            ->names('testimonials');

        Route::resource('testimonial-reports', AdminTestimonialReportController::class)
            ->only(['index', 'destroy'])
            ->names('testimonial-reports');

        // Quizz
        Route::resource('quizzes', AdminQuizController::class)
            ->names('quizzes');

        Route::get('quizzes/{quiz}/questions/create', [AdminQuizQuestionController::class, 'create'])
            ->name('quiz-questions.create');

        Route::post('quizzes/{quiz}/questions', [AdminQuizQuestionController::class, 'store'])
            ->name('quiz-questions.store');

        Route::delete('quizzes/{quiz}/questions/{question}', [AdminQuizQuestionController::class, 'destroy'])
            ->name('quiz-questions.destroy');

    });
