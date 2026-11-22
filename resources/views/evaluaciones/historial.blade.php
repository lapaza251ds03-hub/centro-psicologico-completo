@extends('layouts.app')
@section('titulo', 'Historial Clínico Confidencial')

@section('contenido')
<header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
    <div>
        <h2 style="margin:0;">Historial Clínico Confidencial (RF13 · RN09)</h2>
        <small>Paciente: <strong>{{ $paciente->nombreCompleto() }}</strong> (DNI: {{ $paciente->dni }} · {{ $paciente->fecha_nacimiento->age }} años)</small>
    </div>
    <a href="{{ route('pacientes.index') }}" role="button" class="outline secondary">← Volver a Pacientes</a>
</header>

@forelse($evaluaciones as $ev)
<article>
    <header>
        <strong>Sesión del {{ $ev->cita->fecha_hora->format('d/m/Y H:i') }}</strong> ·
        Atendido por: {{ $ev->cita->psicologo->nombreCompleto() }} ({{ $ev->cita->psicologo->colegiatura }})
    </header>
    <p><strong>Diagnóstico Presuntivo:</strong> {{ $ev->diagnostico_presuntivo }}</p>
    <p><strong>Evolución Terapéutica:</strong><br>{{ $ev->evolucion_terapeutica }}</p>
    @if($ev->pruebas_aplicadas)
        <p><strong>Pruebas Aplicadas:</strong> {{ $ev->pruebas_aplicadas }}</p>
    @endif
    @if($ev->observaciones_confidenciales)
        <blockquote style="margin-bottom:0;"><strong>Notas Confidenciales:</strong> {{ $ev->observaciones_confidenciales }}</blockquote>
    @endif
</article>
@empty
<article><p>Este paciente aún no registra evaluaciones clínicas asociadas a citas atendidas.</p></article>
@endforelse
@endsection