<?php
namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        Gate::before(function (User $user, string $ability) {
            if ($user->role->name === 'Admin') {
                return true;
            }
        });
        Gate::define('usuarios', function (User $user) {
            return $user->role->name === 'Admin';
        });
        Gate::define('Jefe Proyectos', function (User $user) {
            return $user->role->name === 'Jefe Proyectos';
        });
        Gate::define('Visitadoras', function (User $user) {
            return $user->role->name === 'Visitadora Medica';
        });
        Gate::define('Doctores', function (User $user) {
            return $user->role->name === 'Doctor';
        });
    }
}
