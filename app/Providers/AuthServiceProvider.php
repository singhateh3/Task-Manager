<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('admin-action', function (User $user) {
            return $user->role === 'admin' ? Response::allow() : Response::deny('Unauthorized Entry');
        });

        Gate::define('manager-action', function (User $user) {
            return $user->role === 'manager' ? Response::allow() : Response::deny('Unauthorized Entry');
        });

        Gate::define('admin-and-manager-action', function (User $user) {
            return in_array($user->role, ['admin', 'manager']) ? Response::allow() : Response::deny('Unauthorized Entry');
        });
    }
}
