<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IslandController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NusantaraChatController;

use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TestimonialReportController;

use App\Http\Controllers\Admin\IslandStatController;
use App\Http\Controllers\Admin\HistoryController;

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
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [IslandController::class, 'landing'])->name('home');

Route::get('/islands/{island:slug}', [IslandController::class, 'show'])
    ->name('islands.show');

/*
|--------------------------------------------------------------------------
| PUBLIC: TESTIMONI + REPORT
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
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        // HISTORIES
        Route::resource('histories', HistoryController::class)
            ->names('histories')
            ->except(['show']);

        // STATS
        Route::resource('stats', IslandStatController::class)->names('stats');

        Route::post('stats/population/{island}', [IslandStatController::class, 'updatePopulation'])
            ->name('stats.population.update');

        Route::post('stats/{island}/demographics', [IslandStatController::class, 'storeDemographic'])
            ->name('stats.demographics.store');

        Route::delete('stats/{island}/demographics/{demographic}', [IslandStatController::class, 'destroyDemographic'])
            ->name('stats.demographics.destroy');

        // ADMIN: TESTIMONI + REPORT
        Route::resource('testimonials', AdminTestimonialController::class)
            ->only(['index', 'destroy'])
            ->names('testimonials');

        Route::resource('testimonial-reports', AdminTestimonialReportController::class)
            ->only(['index', 'destroy'])
            ->names('testimonial-reports');

        // QUIZ
        Route::resource('quizzes', AdminQuizController::class)->names('quizzes');

        Route::prefix('quizzes/{quiz}')
            ->group(function () {
                Route::get('questions/create', [AdminQuizQuestionController::class, 'create'])
                    ->name('quiz-questions.create');

                Route::post('questions', [AdminQuizQuestionController::class, 'store'])
                    ->name('quiz-questions.store');

                Route::delete('questions/{question}', [AdminQuizQuestionController::class, 'destroy'])
                    ->name('quiz-questions.destroy');
            });
    });
