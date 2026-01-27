<?php

namespace App\Providers;

use App\Services\Media\Contracts\FileUploaderInterface;
use App\Services\Media\FileUploaderService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileUploaderInterface::class, FileUploaderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
