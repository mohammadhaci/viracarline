<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VehicleController;
use App\Http\Middleware\HandleRedirects;
use App\Http\Middleware\PublicMaintenanceMode;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::middleware([HandleRedirects::class, PublicMaintenanceMode::class])->group(function () {
    Route::get('/', fn () => redirect('/'.config('locales.default'), 302));
    Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

    Route::prefix('{locale}')
        ->whereIn('locale', config('locales.supported'))
        ->middleware(SetLocale::class)
        ->group(function () {
            Route::get('/', [PageController::class, 'home'])->name('home');

            Route::get('/fahrzeuge', [VehicleController::class, 'index'])->name('vehicles.index');
            Route::get('/fahrzeuge/{slug}', [VehicleController::class, 'show'])->name('vehicles.show');

            Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
            Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

            Route::post('/anfrage', [LeadController::class, 'store'])
                ->middleware('throttle:10,1')
                ->name('leads.store');

            Route::get('/{slug}', [PageController::class, 'show'])
                ->where('slug', '[a-z0-9\-]+')
                ->name('pages.show');
        });

    // Unmatched URLs still pass through HandleRedirects (old-domain paths).
    Route::fallback(fn () => abort(404));
});
