<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function mostrarLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credenciales, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }

        $usuario = Auth::user();

        if ($usuario->estado !== 'activo') {
            Auth::logout();
            $msg = $usuario->estado === 'pendiente'
                ? 'Tu solicitud aún está pendiente de aprobación por el Director/Administrador.'
                : 'Tu cuenta se encuentra inactiva (licencia/vacaciones).';
            return back()->withErrors(['email' => $msg])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function mostrarSolicitud(): View
    {
        return view('auth.solicitar-acceso');
    }

    public function registrarSolicitud(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'dni' => ['required', 'digits:8', 'unique:users,dni'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'rol' => ['required', 'in:psicologo,recepcionista'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'dni' => $datos['dni'],
            'name' => $datos['name'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
            'estado' => 'pendiente',
            'password' => Hash::make($datos['password']),
        ]);

        return redirect()->route('login')->with('exito', 'Solicitud enviada. Espera la aprobación del Administrador.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}