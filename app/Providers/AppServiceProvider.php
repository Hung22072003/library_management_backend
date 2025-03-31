<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Book\BookRepositoryImplement;
use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\User\UserRepositoryImplement;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepositoryImplement::class);
        $this->app->bind(BookRepositoryInterface::class, BookRepositoryImplement::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });
    }
}
