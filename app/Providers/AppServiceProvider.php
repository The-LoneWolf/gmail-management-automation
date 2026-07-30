<?php

namespace App\Providers;

use App\Services\EmailIntelligence\EmailIntelligenceService;
use App\Services\EmailIntelligence\KeywordEmailIntelligenceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EmailIntelligenceService::class, KeywordEmailIntelligenceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
