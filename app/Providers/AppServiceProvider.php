<?php

namespace App\Providers;

use App\Repositories\ProfileMetricRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserNoteRepository;
use App\Repositories\VideoRepository;
use App\Services\ProfileMetricService;
use App\Services\ServiceManagementService;
use App\Services\UserNoteService;
use App\Services\VideoManagementService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->singleton(ServiceRepository::class, function ($app) {
            return new ServiceRepository($app->make(\App\Models\Service::class));
        });

        $this->app->singleton(VideoRepository::class, function ($app) {
            return new VideoRepository($app->make(\App\Models\Video::class));
        });

        // Services
        $this->app->singleton(ServiceManagementService::class, function ($app) {
            return new ServiceManagementService($app->make(ServiceRepository::class));
        });

        $this->app->singleton(VideoManagementService::class, function ($app) {
            return new VideoManagementService(
                $app->make(VideoRepository::class),
                $app->make(ServiceRepository::class)
            );
        });

        $this->app->bind(ProfileMetricRepository::class, function ($app) {
            return new ProfileMetricRepository();
        });

        $this->app->bind(ProfileMetricService::class, function ($app) {
            return new ProfileMetricService(
                $app->make(ProfileMetricRepository::class)
            );
        });

        $this->app->bind(UserNoteRepository::class, function ($app) {
            return new UserNoteRepository();
        });

        $this->app->bind(UserNoteService::class, function ($app) {
            return new UserNoteService(
                $app->make(UserNoteRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
