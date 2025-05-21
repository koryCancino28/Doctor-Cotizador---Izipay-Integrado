<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class RoleMiddleware
{
   /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$checkRoles): Response
    {
        if (Auth::check()) {
            $userRole = $request->user()->role->name; // Asegúrate de que el usuario tenga el rol asignado

            foreach ($checkRoles as $role) {
                if ($userRole === $role) {
                    return $next($request); // Si el rol coincide, permite el acceso
                }
            }
            // Si no coincide con ninguno de los roles, deniega el acceso
            return redirect('/')->with('error', 'Acceso denegado');
        }
        // Si el usuario no está autenticado, redirigir al login
        return redirect('login');
    }
}
