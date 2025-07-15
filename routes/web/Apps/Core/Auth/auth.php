<?php
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LinkRecuperacionController;
use Laravel\Socialite\Facades\Socialite;


// Mostrar el formulario de login

Route::prefix('login')->group(function () {
    Route::get('/auth', [LoginController::class, 'show'])->name('login.auth');
    Route::post('/auth', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::prefix('register')->group(function () {
    // ✅ Ruta GET para mostrar el formulario de registro
    Route::get('/auth', [RegisterController::class, 'show'])->name('register.auth');


    Route::post('/auth', [RegisterController::class, 'register'])->name('register.post');
});

// Redirige al usuario a la página de autenticación de Google
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('google.login');
// Google llama a esta ruta después de la autenticación (el callback)
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');

Route::prefix('linkRecuperacion')->group(function () {
    // ✅ Ruta GET para mostrar el formulario de registro
    Route::get('/validacionUsuario', [LinkRecuperacionController::class, 'show'])->name('linkRecuperacion.auth');
    Route::post('/validacionUsuario', [LinkRecuperacionController::class, 'LinkRecuperacion'])->name('validacionUsuario.post');
    Route::get('/reset-password/{token}', [LinkRecuperacionController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [LinkRecuperacionController::class, 'reset'])->name('password.update');


});
