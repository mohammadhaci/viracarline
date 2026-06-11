<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\Menu;
use App\Models\Setting;
use App\Observers\LeadObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Lead::observe(LeadObserver::class);

        View::composer('layouts.public', function ($view) {
            $view->with([
                'headerMenu' => Menu::with('items.page')->where('key', 'header')->first(),
                'footerMenu' => Menu::with('items.page')->where('key', 'footer')->first(),
                'siteCompanyName' => Setting::get('site_company_name', 'Vira Car Lines AG'),
                'siteAnalyticsScript' => Setting::get('site_analytics_script'),
                'sitePhone' => Setting::get('site_phone'),
                'siteEmail' => Setting::get('site_email'),
                'siteAddress' => Setting::get('site_address'),
            ]);
        });
    }
}
