<?php

namespace App\Providers;

use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Models\TasksComments;
use App\Policies\TasksCommentsPolicy;
use App\Models\TasksUsers;
use App\Policies\TasksUsersPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(TasksComments::class, TasksCommentsPolicy::class);
        Gate::policy(TasksUsers::class, TasksUsersPolicy::class);
    }
}
