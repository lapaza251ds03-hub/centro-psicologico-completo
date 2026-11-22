<?php

namespace App\Http\Controllers;

use App\Models\Psicologo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PsicologoController extends Controller
{
    public function index(): View
    {
        $psicologos = Psicologo::with('user')->orderByDesc('activo')->orderBy('apellidos')->get();
        $usuariosPsicologos = User::where('rol', 'psicologo')
            ->where('estado', 'activo')
            ->whereDoesntHave('psicologo')
            ->get();

        return view('psicologos.index', compact('psicologos', 'usuariosPsicologos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'user_id' => ['nullable', 'exists:users,id', 'unique:psicologos,user_id'],
            'dni' => ['required', 'digits:8', 'unique:psicologos,dni'],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'especialidad' => ['required', 'in:Clínica,Infantil,Pareja y Familia,Neuropsicología,Educativa'],
            'grupo_etario' => ['required', 'in:ninos,adolescentes,adultos,adulto_mayor'],
            'colegiatura' => ['required', 'string', 'max:15', 'unique:psicologos,colegiatura'],
        ]);

        Psicologo::create([...$datos, 'activo' => true]);

        return back()->with('exito', 'Psicólogo registrado en el equipo terapéutico.');
    }

    // RN04: Activar/Inactivar disponibilidad (vacaciones/licencia)
    public function toggleEstado(Psicologo $psicologo): RedirectResponse
    {
        $psicologo->update(['activo' => ! $psicologo->activo]);
        $estado = $psicologo->activo ? 'habilitado para atención' : 'inactivado (licencia/vacaciones)';

        return back()->with('exito', "Psicólogo {$psicologo->nombreCompleto()} {$estado}.");
    }
}