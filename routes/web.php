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

use App\Http\Controllers\Admin\IslandAboutStatsController;

use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;


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

        // ✅ ENDPOINT JSON UNTUK AUTO-LOAD HEADER (TRIBE PAGES)
        // GET /admin/tribe-pages/lookup?island_id=1&tribe_key=Aceh
        Route::get('tribe-pages/lookup', [HistoryController::class, 'lookupTribePage'])
            ->name('tribe-pages.lookup');

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

        // WARISAN (Heritages)
        Route::get('heritages', [\App\Http\Controllers\Admin\HeritageController::class, 'index'])
            ->name('heritages.index');

        // simpan/update title besar + deskripsi besar per suku
        Route::post('heritages/page', [\App\Http\Controllers\Admin\HeritageController::class, 'savePage'])
            ->name('heritages.page.save');

        // create item warisan (pakaian/rumah_tradisi/senjata_alatmusik)
        Route::post('heritages/item', [\App\Http\Controllers\Admin\HeritageController::class, 'storeItem'])
            ->name('heritages.item.store');

        // update item warisan
        Route::patch('heritages/item/{item}', [\App\Http\Controllers\Admin\HeritageController::class, 'updateItem'])
            ->name('heritages.item.update');

        // delete item warisan
        Route::delete('heritages/item/{item}', [\App\Http\Controllers\Admin\HeritageController::class, 'destroyItem'])
            ->name('heritages.item.destroy');


            // about untuk crud admin
            // ABOUT SUKU (About pages + items)
Route::get('abouts', [\App\Http\Controllers\Admin\TribeAboutController::class, 'index'])
    ->name('abouts.index');

// JSON lookup header about (auto-load saat pilih pulau+suku)
Route::get('about-pages/lookup', [\App\Http\Controllers\Admin\TribeAboutController::class, 'lookupAboutPage'])
    ->name('about-pages.lookup');

// simpan/update header about
Route::post('abouts/page', [\App\Http\Controllers\Admin\TribeAboutController::class, 'savePage'])
    ->name('abouts.page.save');

// create item about
Route::post('abouts/item', [\App\Http\Controllers\Admin\TribeAboutController::class, 'storeItem'])
    ->name('abouts.item.store');

// update item about
Route::patch('abouts/item/{item}', [\App\Http\Controllers\Admin\TribeAboutController::class, 'updateItem'])
    ->name('abouts.item.update');

// delete item about
Route::delete('abouts/item/{item}', [\App\Http\Controllers\Admin\TribeAboutController::class, 'destroyItem'])
    ->name('abouts.item.destroy');


    // ✅ ABOUT PULAU + STATISTIK
    Route::get('/about-stats', [IslandAboutStatsController::class, 'index'])
        ->name('about_stats.index');

    Route::post('/about-stats/{island}/about-page', [IslandAboutStatsController::class, 'upsertAboutPage'])
        ->name('about_stats.about_page');

    Route::post('/about-stats/{island}/items', [IslandAboutStatsController::class, 'storeItem'])
        ->name('about_stats.items.store');

    Route::put('/about-stats/{island}/items/{item}', [IslandAboutStatsController::class, 'updateItem'])
        ->name('about_stats.items.update');

    Route::delete('/about-stats/{island}/items/{item}', [IslandAboutStatsController::class, 'destroyItem'])
        ->name('about_stats.items.destroy');



        // DESTINATIONS
            Route::resource('destinations', AdminDestinationController::class)->except(['show']);

    });
