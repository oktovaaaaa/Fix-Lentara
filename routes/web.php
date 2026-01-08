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



// untuk controller game:
use App\Http\Controllers\PlayerAuthController;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\GameLevelController as AdminGameLevelController;
use App\Http\Controllers\Admin\GameQuestionController as AdminGameQuestionController;

Use App\Http\Controllers\PlayerLeaderboardController;


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

                    Route::get('questions/{question}/edit', [AdminQuizQuestionController::class, 'edit'])
    ->name('quiz-questions.edit');

Route::put('questions/{question}', [AdminQuizQuestionController::class, 'update'])
    ->name('quiz-questions.update');

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


        // GAME LEVELS + QUESTIONS admin
        // GAME LEVELS
        Route::get('game-levels', [AdminGameLevelController::class, 'index'])->name('game-levels.index');
        Route::post('game-levels', [AdminGameLevelController::class, 'store'])->name('game-levels.store');
        Route::get('game-levels/{gameLevel}/edit', [AdminGameLevelController::class, 'edit'])->name('game-levels.edit');
        Route::put('game-levels/{gameLevel}', [AdminGameLevelController::class, 'update'])->name('game-levels.update');
        Route::delete('game-levels/{gameLevel}', [AdminGameLevelController::class, 'destroy'])->name('game-levels.destroy');

        // GAME QUESTIONS per level
        Route::get('game-levels/{level}/questions', [AdminGameQuestionController::class, 'index'])->name('game-questions.index');
        Route::post('game-levels/{level}/questions', [AdminGameQuestionController::class, 'store'])->name('game-questions.store');

        // ✅ BARU: EDIT + UPDATE SOAL
        Route::get('game-levels/{level}/questions/{question}/edit', [AdminGameQuestionController::class, 'edit'])->name('game-questions.edit');
        Route::put('game-levels/{level}/questions/{question}', [AdminGameQuestionController::class, 'update'])->name('game-questions.update');

        Route::delete('game-levels/{level}/questions/{question}', [AdminGameQuestionController::class, 'destroy'])->name('game-questions.destroy');
    });


/*
|--------------------------------------------------------------------------
| PLAYER AUTH (GAME)
|--------------------------------------------------------------------------
*/
Route::get('/daftar', [PlayerAuthController::class, 'showRegister'])->name('player.register');
Route::post('/daftar', [PlayerAuthController::class, 'register'])->name('player.register.post');

Route::get('/masuk', [PlayerAuthController::class, 'showLogin'])->name('player.login');
Route::post('/masuk', [PlayerAuthController::class, 'login'])->name('player.login.post');

Route::post('/keluar', [PlayerAuthController::class, 'logout'])->name('player.logout')->middleware('player');

/*
|--------------------------------------------------------------------------
| GAME (PLAYER)
|--------------------------------------------------------------------------
*/
Route::get('/belajar', [GameController::class, 'learn'])->name('game.learn')->middleware('player');
Route::get('/belajar/level/{level}', [GameController::class, 'play'])->name('game.play')->middleware('player');

// NOTE: submit batch tidak dipakai untuk flow baru, tapi aku biarkan biar gak nabrak.
// Kamu bisa hapus nanti kalau sudah yakin tidak dipakai.
Route::post('/belajar/level/{level}/submit', [GameController::class, 'submit'])->name('game.submit')->middleware('player');

// cek per soal (AJAX)
Route::post('/belajar/level/{level}/check', [GameController::class, 'check'])
    ->name('game.check')
    ->middleware('player');

// refill hati (10 uang)
Route::post('/hati/isi-ulang', [GameController::class, 'refillHearts'])
    ->name('game.hearts.refill')
    ->middleware('player');

// leaderboard (XP)
Route::get('/papan-peringkat', [PlayerLeaderboardController::class, 'index'])
    ->name('game.leaderboard')
    ->middleware('player');

Route::get('/profil', [PlayerProfileController::class, 'edit'])->name('player.profile')->middleware('player');
Route::post('/profil', [PlayerProfileController::class, 'update'])->name('player.profile.update')->middleware('player');

Route::get('/belajar/panduan', [GameController::class, 'guide'])
    ->name('game.guide')
    ->middleware('player');
