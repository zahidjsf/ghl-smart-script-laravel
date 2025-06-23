<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\AdminPanel\ProjectRepositoryInterface;
use App\Repositories\AdminPanel\ProjectRepository;
use App\Repositories\Interfaces\AdminPanel\AccountRepositoryInterface;
use App\Repositories\AdminPanel\AccountRepository;
use App\Repositories\AdminPanel\PackageRepository;
use App\Repositories\AdminPanel\SnapshotRepository;
use App\Repositories\FrontPanel\CustomValueRepository;
use App\Repositories\FrontPanel\CVSmartRewardRepository;
use App\Repositories\Interfaces\AdminPanel\PackageRepositoryInterface;
use App\Repositories\Interfaces\AdminPanel\SnapshotRepositoryInterface;
use App\Repositories\Interfaces\FrontPanel\CustomValueRepositoryInterface;
use App\Repositories\Interfaces\FrontPanel\CVSmartRewardRepositoryInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // AdminPanel
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
        $this->app->bind(SnapshotRepositoryInterface::class, SnapshotRepository::class);
        // FrontPanel
        $this->app->bind(CustomValueRepositoryInterface::class, CustomValueRepository::class);
        $this->app->bind(CVSmartRewardRepositoryInterface::class, CVSmartRewardRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Auth::check()) {
            App::setLocale(Auth::user()->locale);
        }
    }
}
