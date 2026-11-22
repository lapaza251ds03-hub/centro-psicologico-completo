<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\EvaluacionClinica;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EvaluacionClinicaController extends Controller
{
    public function create(Cita $cita): View|RedirectResponse
    {
        $usuario = Auth::user();

        // RN09: Confidencialidad clínica (solo psicólogo tratante o admin)
        if (! $usuario->esAdmin() && (! $usuario->psicologo || $cita->psicologo_id !== $usuario->psicologo->id)) {
            abort(403, 'RN09: Solo el psicólogo tratante o el Administrador pueden registrar esta evaluación.');
        }

        if ($cita->estado !== 'atendida') {
            return redirect()->route('citas.index')->withErrors(['error' => 'La cita debe estar en estado ATENDIDA para registrar su evaluación clínica.']);
        }

        // RN08: Relación estricta 1:1
        if ($cita->evaluacionClinica) {
            return redirect()->route('evaluaciones.historial', $cita->paciente_id)
                ->with('exito', 'Esta cita ya cuenta con su evaluación clínica registrada.');
        }

        return view('evaluaciones.create', compact('cita'));
    }

    public function store(Request $request, Cita $cita): RedirectResponse
    {
        $usuario = Auth::user();

        if (! $usuario->esAdmin() && (! $usuario->psicologo || $cita->psicologo_id !== $usuario->psicologo->id)) {
            abort(403, 'RN09: Acceso denegado por confidencialidad clínica.');
        }

        if ($cita->estado !== 'atendida' || $cita->evaluacionClinica) {
            return redirect()->route('citas.index')->withErrors(['error' => 'RN08: No se puede duplicar la evaluación clínica de esta cita.']);
        }

        $datos = $request->validate([
            'diagnostico_presuntivo' => ['required', 'string', 'max:255'],
            'evolucion_terapeutica' => ['required', 'string'],
            'pruebas_aplicadas' => ['nullable', 'string'],
            'observaciones_confidenciales' => ['nullable', 'string'],
        ]);

        EvaluacionClinica::create([
            'cita_id' => $cita->id,
            ...$datos,
        ]);

        return redirect()->route('evaluaciones.historial', $cita->paciente_id)
            ->with('exito', 'Evaluación clínica guardada bajo secreto profesional.');
    }

    // RF13 & RN09: Historial clínico confidencial del paciente
    public function historial(Paciente $paciente): View
    {
        $usuario = Auth::user();

        // Recepcionista no tiene acceso al detalle diagnóstico (RN09)
        if ($usuario->esRecepcionista()) {
            abort(403, 'RN09: El personal de recepción no está autorizado para visualizar diagnósticos clínicos.');
        }

        $evaluaciones = EvaluacionClinica::with(['cita.psicologo'])
            ->whereHas('cita', function ($q) use ($paciente, $usuario) {
                $q->where('paciente_id', $paciente->id);
                if ($usuario->esPsicologo() && $usuario->psicologo) {
                    $q->where('psicologo_id', $usuario->psicologo->id);
                }
            })
            ->latest()
            ->get();

        return view('evaluaciones.historial', compact('paciente', 'evaluaciones'));
    }
}