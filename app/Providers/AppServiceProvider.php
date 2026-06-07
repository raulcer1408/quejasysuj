<?php

namespace App\Providers;

use App\Models\User;
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
        Gate::define('administrar-usuarios', function (User $user) {
            return $user->hasRole('superusuario', 'sistemas');
        });

        Gate::define('enviar-quejas', function (User $user) {
            return $user->hasRole('superusuario', 'sistemas', 'visitante') || $user->isDocente();
        });

        Gate::define('revisar-quejas', function (User $user) {
            return $user->hasRole('superusuario', 'sistemas') || $user->isRevisor();
        });

        Gate::define('coordinar-quejas', function (User $user) {
            return $user->hasRole('superusuario', 'sistemas') || $user->isCoordinador();
        });

        Gate::define('resolver-quejas', function (User $user) {
            return $user->hasRole('superusuario', 'sistemas') || $user->isJefeUnidad();
        });

        // Gates de visibilidad de menú (excluyen superusuario y sistemas)
        Gate::define('menu-solicitudes', function (User $user) {
            return $user->hasRole('visitante');
        });

        Gate::define('menu-docente', function (User $user) {
            return $user->isDocente();
        });

        Gate::define('menu-jefatura', function (User $user) {
            return $user->isJefeUnidad();
        });

        Gate::define('menu-coordinacion', function (User $user) {
            return $user->isCoordinador();
        });

        Gate::define('menu-revision', function (User $user) {
            return $user->isRevisor() && !$user->isUsuarioEje();
        });

        Gate::define('menu-buzon-revista', function (User $user) {
            return ($user->isMiembroComision() || $user->role === User::ROLE_RESPONSABLE_REVISTA)
                && !$user->isUsuarioEje();
        });

        Gate::define('menu-usuario-eje', function (User $user) {
            return $user->isUsuarioEje() && !$user->isMiembroComision();
        });

        Gate::define('menu-comision-eje', function (User $user) {
            return $user->isUsuarioEje() && $user->isMiembroComision();
        });

        Gate::define('ver-reportes', function (User $user) {
            return $user->hasRole('superusuario', 'sistemas') || $user->isRevisor() || $user->isJefeUnidad();
        });

        Gate::define('ver-historial-propio', function (User $user) {
            return true; // Todos los usuarios autenticados
        });
    }
}
