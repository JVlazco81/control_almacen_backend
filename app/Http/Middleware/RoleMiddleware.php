<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // 2) Verificar la relación rol y comparar
        if (! $user->rol || $user->rol->rol !== $rol) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        return $next($request);
    }
}
