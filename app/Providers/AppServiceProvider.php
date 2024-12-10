<?php

namespace App\Providers;

use App\Repositories\ChallengeRepository;
use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ChallengeRepositoryInterface::class, ChallengeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}