<?php
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LinkRecuperacionController;
use App\Http\Controllers\HomeController;

// --- RUTAS PARA INVITADOS (USUARIOS SIN SESIÓN) ---
Route::middleware('guest')->group(function () {

    Route::get('/login', fn() => redirect()->route('login.auth'))->name('login');

    Route::prefix('login')->group(function () {
        Route::get('/auth', [LoginController::class, 'show'])->name('login.auth');
        Route::post('/auth', [LoginController::class, 'login'])->name('login.post');
    });

    Route::prefix('register')->group(function () {
        Route::get('/auth', [RegisterController::class, 'show'])->name('register.auth');
        Route::post('/auth', [RegisterController::class, 'register'])->name('register.post');
    });

    Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');

    Route::prefix('linkRecuperacion')->group(function () {
        Route::get('/validacionUsuario', [LinkRecuperacionController::class, 'show'])->name('linkRecuperacion.auth');
        Route::post('/validacionUsuario', [LinkRecuperacionController::class, 'LinkRecuperacion'])->name('validacionUsuario.post');
        Route::get('/reset-password/{token}', [LinkRecuperacionController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [LinkRecuperacionController::class, 'reset'])->name('password.update');
    });
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

});
