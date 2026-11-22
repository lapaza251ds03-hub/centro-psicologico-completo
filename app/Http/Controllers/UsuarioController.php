<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $pendientes = User::where('estado', 'pendiente')->latest()->get();
        $activos = User::where('estado', '!=', 'pendiente')->orderBy('name')->get();

        return view('usuarios.index', compact('pendientes', 'activos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'dni' => ['required', 'digits:8', 'unique:users,dni'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'rol' => ['required', 'in:admin,psicologo,recepcionista'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            ...$datos,
            'estado' => 'activo',
            'password' => Hash::make($datos['password']),
        ]);

        return back()->with('exito', 'Personal registrado y activado en planilla.');
    }

    public function aprobar(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'rol' => ['required', 'in:admin,psicologo,recepcionista'],
        ]);

        $usuario->update(['rol' => $datos['rol'], 'estado' => 'activo']);
        return back()->with('exito', "Acceso aprobado para {$usuario->name}.");
    }

    public function cambiarEstado(User $usuario): RedirectResponse
    {
        if ($usuario->id === Auth::id()) {
            return back()->withErrors(['error' => 'No puedes desactivar tu propia cuenta.']);
        }

        $nuevoEstado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->update(['estado' => $nuevoEstado]);

        return back()->with('exito', "Estado de {$usuario->name} actualizado a {$nuevoEstado}.");
    }
}