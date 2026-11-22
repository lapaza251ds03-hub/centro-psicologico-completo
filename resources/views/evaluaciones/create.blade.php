@extends('layouts.app')
@section('titulo', 'Registrar Evaluación Clínica · Confidencial')

@section('contenido')
<article style="max-width: 760px; margin: 0 auto;">
    <header>
        <h3 style="margin:0;">Registro de Evaluación Clínica (RF12 · RN08)</h3>
        <small>Paciente: <strong>{{ $cita->paciente->nombreCompleto() }}</strong> · Fecha de sesión: {{ $cita->fecha_hora->format('d/m/Y H:i') }}</small>
    </header>
    <form action="{{ route('evaluaciones.store', $cita) }}" method="POST">
        @csrf
        <label>Diagnóstico Presuntivo (CIE-10 / DSM-5)
            <input type="text" name="diagnostico_presuntivo" value="{{ old('diagnostico_presuntivo') }}" required>
        </label>
        <label>Evolución Terapéutica
            <textarea name="evolucion_terapeutica" rows="4" required>{{ old('evolucion_terapeutica') }}</textarea>
        </label>
        <label>Pruebas Psicológicas Aplicadas (opcional)
            <textarea name="pruebas_aplicadas" rows="2">{{ old('pruebas_aplicadas') }}</textarea>
        </label>
        <label>Observaciones Confidenciales del Psicólogo Tratante (RN09)
            <textarea name="observaciones_confidenciales" rows="2">{{ old('observaciones_confidenciales') }}</textarea>
        </label>
        <div class="grid">
            <a href="{{ route('citas.index') }}" role="button" class="outline secondary">Cancelar</a>
            <button type="submit">Guardar Evaluación Clínica</button>
        </div>
    </form>
</article>
@endsection