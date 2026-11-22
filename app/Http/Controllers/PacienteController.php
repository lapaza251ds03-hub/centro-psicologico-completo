<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->input('q');

        $pacientes = Paciente::when($busqueda, function ($query, $q) {
                $query->where('dni', 'like', "%{$q}%")
                      ->orWhere('nombres', 'ilike', "%{$q}%")
                      ->orWhere('apellidos', 'ilike', "%{$q}%");
            })
            ->orderByDesc('activo')
            ->orderBy('apellidos')
            ->get();

        return view('pacientes.index', compact('pacientes', 'busqueda'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarPaciente($request);
        Paciente::create($datos);

        return back()->with('exito', 'Paciente registrado correctamente.');
    }

    public function edit(Paciente $paciente): View
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $datos = $this->validarPaciente($request, $paciente->id);
        $paciente->update($datos);

        return redirect()->route('pacientes.index')->with('exito', 'Datos del paciente actualizados.');
    }

    // RF05: Inactivación lógica (sin borrado físico)
    public function toggleEstado(Paciente $paciente): RedirectResponse
    {
        $paciente->update(['activo' => ! $paciente->activo]);
        $estadoTexto = $paciente->activo ? 'reactivado' : 'inactivado';

        return back()->with('exito', "Paciente {$paciente->nombreCompleto()} {$estadoTexto}.");
    }

    private function validarPaciente(Request $request, ?int $pacienteId = null): array
    {
        $esMenor = false;
        if ($request->filled('fecha_nacimiento')) {
            $esMenor = Carbon::parse($request->input('fecha_nacimiento'))->age < 18;
        }

        // RN03: Menores de 18 años exigen apoderado y consentimiento informado
        $reglasApoderado = $esMenor ? ['required'] : ['nullable'];

        $datos = $request->validate([
            'dni' => ['required', 'digits:8', 'unique:pacientes,dni,' . $pacienteId],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'sexo' => ['required', 'in:M,F'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'motivo_consulta' => ['required', 'string'],
            'apoderado_nombre' => [...$reglasApoderado, 'string', 'max:120'],
            'apoderado_dni' => [...$reglasApoderado, 'digits:8'],
            'consentimiento_informado' => $esMenor ? ['accepted'] : ['nullable'],
        ], [
            'apoderado_nombre.required' => 'RN03: Al ser menor de edad, el nombre del apoderado es obligatorio.',
            'apoderado_dni.required' => 'RN03: Al ser menor de edad, el DNI del apoderado es obligatorio.',
            'consentimiento_informado.accepted' => 'RN03: Debe marcar el consentimiento informado firmado por el apoderado.',
        ]);

        $datos['consentimiento_informado'] = $request->boolean('consentimiento_informado');

        return $datos;
    }
}