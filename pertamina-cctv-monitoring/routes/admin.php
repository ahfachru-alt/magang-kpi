<?php

use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
});

// Admin Dashboard Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    
    // User Management
    Route::get('/user', \App\Livewire\Admin\User\Index::class)->name('user.index');
    Route::get('/user/create', \App\Livewire\Admin\User\Create::class)->name('user.create');
    Route::get('/user/{user}/edit', \App\Livewire\Admin\User\Edit::class)->name('user.edit');
    
    // Table Management
    Route::get('/table', \App\Livewire\Admin\Table\Index::class)->name('table.index');
    Route::get('/table/create', \App\Livewire\Admin\Table\Create::class)->name('table.create');
    Route::get('/table/{table}/edit', \App\Livewire\Admin\Table\Edit::class)->name('table.edit');
    
    // Maps Management
    Route::get('/maps', \App\Livewire\Admin\Maps\Index::class)->name('maps.index');
    Route::get('/maps/create', \App\Livewire\Admin\Maps\Create::class)->name('maps.create');
    Route::get('/maps/{maps}/edit', \App\Livewire\Admin\Maps\Edit::class)->name('maps.edit');
    
    // Location Management
    Route::get('/location', \App\Livewire\Admin\Location\Index::class)->name('location.index');
    Route::get('/location/create', \App\Livewire\Admin\Location\Create::class)->name('location.create');
    Route::get('/location/{location}/edit', \App\Livewire\Admin\Location\Edit::class)->name('location.edit');
    
    // Contact Management
    Route::get('/contact', \App\Livewire\Admin\Contact\Index::class)->name('contact.index');
    Route::get('/contact/create', \App\Livewire\Admin\Contact\Create::class)->name('contact.create');
    Route::get('/contact/{contact}/edit', \App\Livewire\Admin\Contact\Edit::class)->name('contact.edit');
    
    // Message
    Route::get('/message', \App\Livewire\Admin\Message::class)->name('message');
    
    // Notification
    Route::get('/notification', \App\Livewire\Admin\Notification\Index::class)->name('notification.index');
    Route::get('/notification/{notification}', \App\Livewire\Admin\Notification\Show::class)->name('notification.show');

    // Export routes
    Route::get('/export/buildings', [App\Http\Controllers\ExportController::class, 'buildings'])->name('export.buildings');
    Route::get('/export/rooms', [App\Http\Controllers\ExportController::class, 'rooms'])->name('export.rooms');
    Route::get('/export/cctvs', [App\Http\Controllers\ExportController::class, 'cctvs'])->name('export.cctvs');
    Route::get('/export/all', [App\Http\Controllers\ExportController::class, 'all'])->name('export.all');
});