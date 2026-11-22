<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\EvaluacionClinica;
use App\Models\Paciente;
use App\Models\Psicologo;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        $kpis = [
            'pacientes_activos' => Paciente::where('activo', true)->count(),
            'psicologos_activos' => Psicologo::where('activo', true)->count(),
            'citas_hoy' => Cita::whereDate('fecha_hora', today())->where('estado', '!=', 'cancelada')->count(),
            'citas_atendidas' => Cita::where('estado', 'atendida')->count(),
            'evaluaciones_total' => EvaluacionClinica::count(),
        ];

        $proximasCitas = Cita::with(['paciente', 'psicologo'])
            ->where('fecha_hora', '>=', now()->startOfDay())
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('fecha_hora')
            ->limit(8)
            ->get();

        return view('dashboard', compact('kpis', 'proximasCitas'));
    }

    // RF15: Exportación de reporte de atenciones en CSV (compatible con Excel)
    public function exportarCsv(): StreamedResponse
    {
        $citas = Cita::with(['paciente', 'psicologo', 'evaluacionClinica'])
            ->orderByDesc('fecha_hora')
            ->get();

        return response()->streamDownload(function () use ($citas) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM para Excel
            fputcsv($handle, ['ID Cita', 'Fecha y Hora', 'Paciente DNI', 'Paciente', 'Psicólogo', 'Especialidad', 'Modalidad', 'Estado', 'Evaluación Registrada'], ';');

            foreach ($citas as $c) {
                fputcsv($handle, [
                    $c->id,
                    $c->fecha_hora->format('d/m/Y H:i'),
                    $c->paciente->dni,
                    $c->paciente->nombreCompleto(),
                    $c->psicologo->nombreCompleto(),
                    $c->psicologo->especialidad,
                    ucfirst($c->modalidad),
                    strtoupper($c->estado),
                    $c->evaluacionClinica ? 'SÍ' : 'NO',
                ], ';');
            }
            fclose($handle);
        }, 'reporte_citas_centro_psicologico.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // RF15: Vista de reporte institucional listo para imprimir / guardar como PDF
    public function reporteImprimible(): View|Response
    {
        $citas = Cita::with(['paciente', 'psicologo'])->orderByDesc('fecha_hora')->get();
        return view('reportes.citas-pdf', compact('citas'));
    }
}