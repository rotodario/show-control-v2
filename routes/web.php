<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleCalendarImportController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\PlatformMailController;
use App\Http\Controllers\PlatformSettingsController;
use App\Http\Controllers\PlatformUserController;
use App\Http\Controllers\PlatformToolController;
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
    Route::redirect('/account', '/account/profile')->name('account.index');
    Route::get('/account/profile', [AccountSettingsController::class, 'profile'])->name('account.profile');

    Route::middleware('permission:manage account settings')->group(function () {
        Route::get('/account/alerts', [AccountSettingsController::class, 'alerts'])->name('account.alerts');
        Route::put('/account/alerts', [AccountSettingsController::class, 'updateAlerts'])->name('account.alerts.update');
        Route::get('/account/pdf', [AccountSettingsController::class, 'pdf'])->name('account.pdf');
        Route::put('/account/pdf', [AccountSettingsController::class, 'updatePdf'])->name('account.pdf.update');
        Route::get('/account/preferences', [AccountSettingsController::class, 'preferences'])->name('account.preferences');
        Route::put('/account/preferences', [AccountSettingsController::class, 'updatePreferences'])->name('account.preferences.update');
        Route::get('/account/mail', [AccountSettingsController::class, 'mail'])->name('account.mail');
        Route::put('/account/mail', [AccountSettingsController::class, 'updateMail'])->name('account.mail.update');
    });

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
        Route::get('/shows-map', [ShowController::class, 'map'])->name('shows.map');
        Route::post('/shows-map/sync', [ShowController::class, 'syncMap'])->name('shows.map.sync');
        Route::resource('shows', ShowController::class);
        Route::put('/shows/{show}/preview-route', [ShowController::class, 'previewRoute'])->name('shows.preview-route');
        Route::post('/shows/{show}/send-roadmap-mail', [ShowController::class, 'sendRoadmapMail'])->name('shows.send-roadmap-mail');
        Route::post('/shows/{show}/send-alert-mail', [ShowController::class, 'sendAlertMail'])->name('shows.send-alert-mail');
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

    Route::middleware('permission:manage platform users')->group(function () {
        Route::get('/platform/users', [PlatformUserController::class, 'index'])->name('platform.users.index');
        Route::put('/platform/users/{user}', [PlatformUserController::class, 'update'])->name('platform.users.update');
    });

    Route::middleware('permission:manage platform settings')->group(function () {
        Route::get('/platform/settings', [PlatformSettingsController::class, 'edit'])->name('platform.settings.edit');
        Route::put('/platform/settings', [PlatformSettingsController::class, 'update'])->name('platform.settings.update');
        Route::get('/platform/mail', [PlatformMailController::class, 'edit'])->name('platform.mail.edit');
        Route::put('/platform/mail', [PlatformMailController::class, 'update'])->name('platform.mail.update');
        Route::get('/platform/tools', [PlatformToolController::class, 'index'])->name('platform.tools.index');
        Route::post('/platform/tools/backup', [PlatformToolController::class, 'backup'])->name('platform.tools.backup');
        Route::get('/platform/tools/backups/{filename}', [PlatformToolController::class, 'download'])->name('platform.tools.download');
        Route::post('/platform/tools/restore', [PlatformToolController::class, 'restore'])->name('platform.tools.restore');
    });
});

require __DIR__.'/auth.php';
