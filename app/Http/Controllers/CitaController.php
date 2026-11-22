<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Psicologo;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CitaController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = Auth::user();
        $fecha = $request->input('fecha');

        $citas = Cita::with(['paciente', 'psicologo', 'evaluacionClinica'])
            ->when($usuario->esPsicologo() && $usuario->psicologo, function ($q) use ($usuario) {
                $q->where('psicologo_id', $usuario->psicologo->id);
            })
            ->when($fecha, fn ($q) => $q->whereDate('fecha_hora', $fecha))
            ->orderByDesc('fecha_hora')
            ->get();

        $pacientes = Paciente::where('activo', true)->orderBy('apellidos')->get();
        // RN04: Solo psicólogos activos pueden recibir citas
        $psicologos = Psicologo::where('activo', true)->orderBy('apellidos')->get();

        return view('citas.index', compact('citas', 'pacientes', 'psicologos', 'fecha'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'psicologo_id' => ['required', 'exists:psicologos,id'],
            'fecha_hora' => ['required', 'date', 'after_or_equal:now'],
            'modalidad' => ['required', 'in:presencial,virtual'],
            'tipo_sesion' => ['required', 'string', 'max:80'],
        ]);

        $paciente = Paciente::findOrFail($datos['paciente_id']);
        $psicologo = Psicologo::findOrFail($datos['psicologo_id']);

        // RN04: Validar psicólogo activo
        if (! $psicologo->activo) {
            return back()->withErrors(['psicologo_id' => 'RN04: El psicólogo seleccionado está inactivo y no recibe citas.'])->withInput();
        }

        // RF08 & RN05: Triaje automático por grupo etario
        if ($psicologo->grupo_etario !== $paciente->grupoEtario()) {
            return back()->withErrors([
                'psicologo_id' => "RN05 (Triaje Etario): El paciente pertenece al grupo '{$paciente->grupoEtario()}' y el psicólogo atiende '{$psicologo->grupo_etario}'.",
            ])->withInput();
        }

        // RN06: Bloque institucional fijo de 60 minutos
        $inicio = Carbon::parse($datos['fecha_hora']);
        $fin = $inicio->copy()->addMinutes(60);

        // RF10 & RN07: Prevención de cruces para psicólogo o paciente
        $crucePsicologo = Cita::where('psicologo_id', $psicologo->id)
            ->where('estado', '!=', 'cancelada')
            ->where('fecha_hora', '<', $fin)
            ->where('fecha_hora_fin', '>', $inicio)
            ->exists();

        if ($crucePsicologo) {
            return back()->withErrors(['fecha_hora' => 'RN07: El psicólogo ya tiene una sesión programada en ese bloque de 60 minutos.'])->withInput();
        }

        $crucePaciente = Cita::where('paciente_id', $paciente->id)
            ->where('estado', '!=', 'cancelada')
            ->where('fecha_hora', '<', $fin)
            ->where('fecha_hora_fin', '>', $inicio)
            ->exists();

        if ($crucePaciente) {
            return back()->withErrors(['fecha_hora' => 'RN07: El paciente ya tiene otra cita programada en ese horario.'])->withInput();
        }

        Cita::create([
            ...$datos,
            'fecha_hora' => $inicio,
            'fecha_hora_fin' => $fin,
            'estado' => 'pendiente',
        ]);

        return back()->with('exito', 'Cita agendada correctamente en bloque de 60 minutos.');
    }

    // RF11: Cambio de estado de cita
    public function cambiarEstado(Request $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validate([
            'estado' => ['required', 'in:pendiente,confirmada,atendida,cancelada'],
        ]);

        $cita->update(['estado' => $datos['estado']]);

        return back()->with('exito', "Estado de la cita actualizado a {$datos['estado']}.");
    }
}