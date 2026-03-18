<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleCalendarImportController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\PublicSharedAccessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\ShowDocumentController;
use App\Http\Controllers\ShowSectionMessageController;
use App\Http\Controllers\SharedAccessController;
use App\Http\Controllers\TourContactController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TourDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install', [InstallController::class, 'store'])->name('install.store');

Route::get('/shared/{token}', [PublicSharedAccessController::class, 'index'])->name('public-access.index');
Route::post('/shared/{token}/shows', [PublicSharedAccessController::class, 'storeShow'])->name('public-access.shows.store');
Route::get('/shared/{token}/shows/{show}', [PublicSharedAccessController::class, 'show'])->name('public-access.shows.show');
Route::put('/shared/{token}/shows/{show}', [PublicSharedAccessController::class, 'updateShow'])->name('public-access.shows.update');
Route::delete('/shared/{token}/shows/{show}', [PublicSharedAccessController::class, 'destroyShow'])->name('public-access.shows.destroy');
Route::post('/shared/{token}/shows/{show}/documents', [PublicSharedAccessController::class, 'storeDocument'])->name('public-access.documents.store');
Route::get('/shared/{token}/documents/{document}', [PublicSharedAccessController::class, 'document'])->name('public-access.documents.show');
Route::post('/shared/{token}/shows/{show}/section-messages', [ShowSectionMessageController::class, 'storePublic'])->name('public-access.section-messages.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('permission:manage tours')->group(function () {
        Route::resource('tours', TourController::class);
        Route::get('/tours-google-calendar', [GoogleCalendarImportController::class, 'index'])->name('tours.google-calendar.index');
        Route::post('/tours-google-calendar/import', [GoogleCalendarImportController::class, 'import'])->name('tours.google-calendar.import');

        Route::post('/tours/{tour}/contacts', [TourContactController::class, 'store'])->name('tours.contacts.store');
        Route::get('/tours/{tour}/contacts/{contact}/edit', [TourContactController::class, 'edit'])->name('tours.contacts.edit');
        Route::put('/tours/{tour}/contacts/{contact}', [TourContactController::class, 'update'])->name('tours.contacts.update');
        Route::delete('/tours/{tour}/contacts/{contact}', [TourContactController::class, 'destroy'])->name('tours.contacts.destroy');

        Route::post('/tours/{tour}/documents', [TourDocumentController::class, 'store'])->name('tours.documents.store');
        Route::get('/tours/{tour}/documents/{document}', [TourDocumentController::class, 'show'])->name('tours.documents.show');
        Route::get('/tours/{tour}/documents/{document}/edit', [TourDocumentController::class, 'edit'])->name('tours.documents.edit');
        Route::put('/tours/{tour}/documents/{document}', [TourDocumentController::class, 'update'])->name('tours.documents.update');
        Route::delete('/tours/{tour}/documents/{document}', [TourDocumentController::class, 'destroy'])->name('tours.documents.destroy');
    });

    Route::middleware('permission:manage shows')->group(function () {
        Route::get('/shows-calendar', [ShowController::class, 'calendar'])->name('shows.calendar');
        Route::resource('shows', ShowController::class);
        Route::get('/shows/{show}/pdf', [ShowController::class, 'pdf'])->name('shows.pdf');
        Route::post('/shows/{show}/documents', [ShowDocumentController::class, 'store'])->name('shows.documents.store');
        Route::post('/shows/{show}/section-messages', [ShowSectionMessageController::class, 'store'])->name('shows.section-messages.store');
        Route::get('/shows/{show}/documents/{document}', [ShowDocumentController::class, 'show'])->name('shows.documents.show');
        Route::get('/shows/{show}/documents/{document}/edit', [ShowDocumentController::class, 'edit'])->name('shows.documents.edit');
        Route::put('/shows/{show}/documents/{document}', [ShowDocumentController::class, 'update'])->name('shows.documents.update');
        Route::delete('/shows/{show}/documents/{document}', [ShowDocumentController::class, 'destroy'])->name('shows.documents.destroy');
    });

    Route::middleware('permission:manage access')->group(function () {
        Route::get('/shared-accesses', [SharedAccessController::class, 'index'])->name('shared-accesses.index');
        Route::post('/shared-accesses', [SharedAccessController::class, 'store'])->name('shared-accesses.store');
        Route::delete('/shared-accesses/{sharedAccess}', [SharedAccessController::class, 'destroy'])->name('shared-accesses.destroy');
    });
});

require __DIR__.'/auth.php';
