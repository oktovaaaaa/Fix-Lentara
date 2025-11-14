<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IslandController;
use App\Http\Controllers\Admin\IslandStatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NusantaraChatController;

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

        // Dashboard sederhana (pakai view saja dulu)
        // -> buat file resources/views/admin/dashboard.blade.php sendiri
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        // CRUD Statistik pulau (index, create, store, show, edit, update, destroy)
        Route::resource('stats', IslandStatController::class)
            ->names('stats');

        // Tambahan route khusus statistik (population & demographics)
        Route::post('stats/population/{island}', [IslandStatController::class, 'updatePopulation'])
            ->name('stats.population.update');

        Route::post('stats/{island}/demographics', [IslandStatController::class, 'storeDemographic'])
            ->name('stats.demographics.store');

        Route::delete('stats/{island}/demographics/{demographic}', [IslandStatController::class, 'destroyDemographic'])
            ->name('stats.demographics.destroy');
    });
