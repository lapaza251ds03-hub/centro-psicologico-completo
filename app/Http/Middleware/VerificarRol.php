<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = Auth::user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        // RN01: Planilla cerrada, solo usuarios con estado activo operan el sistema
        if ($usuario->estado !== 'activo') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está pendiente de aprobación o se encuentra inactiva.',
            ]);
        }

        // RF03: Control de acceso por rol
        if (! empty($roles) && ! in_array($usuario->rol, $roles, true)) {
            abort(403, 'Acceso denegado: tu rol no tiene permisos para este módulo.');
        }

        return $next($request);
    }
}