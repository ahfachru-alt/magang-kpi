<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\MapsController;
use App\Http\Controllers\User\LocationController;
use App\Http\Controllers\User\CctvController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\NotificationController;

// User Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', \App\Livewire\Auth\VerifyEmail::class)->name('verification.notice');
    Route::get('/confirm-password', \App\Livewire\Auth\ConfirmPassword::class)->name('password.confirm');
});

// User Dashboard Routes
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', \App\Livewire\User\Dashboard::class)->name('dashboard');
    Route::get('/maps', \App\Livewire\User\Maps\Index::class)->name('maps');
    Route::get('/location', \App\Livewire\User\Location\Index::class)->name('location');
    Route::get('/room', \App\Livewire\User\Room\Index::class)->name('room');
    Route::get('/cctv', \App\Livewire\User\Cctv\Index::class)->name('cctv');
    Route::get('/cctv/{cctv}/stream', \App\Livewire\User\Cctv\ShowStream::class)->name('cctv.stream');
    Route::get('/contact', \App\Livewire\User\Contact\Index::class)->name('contact');
    Route::get('/message', \App\Livewire\User\Message::class)->name('message');
    Route::get('/notification', \App\Livewire\User\Notification\Index::class)->name('notification');
    Route::get('/notification/{notification}', \App\Livewire\User\Notification\Show::class)->name('notification.show');
});