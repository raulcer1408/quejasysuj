<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        if ($user->isSuperusuario() || $user->isSistemas()) {
            return $next($request);
        }

        // Permite acceso a rutas que declaran 'miembro_comision' si el usuario tiene el flag activo
        if ($user->es_miembro_comision && in_array('miembro_comision', $roles)) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
