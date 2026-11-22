@extends('layouts.app')
@section('titulo', 'Dashboard · Centro Psicológico Integral')

@section('contenido')
<header style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0;">Panel Institucional (RF14)</h2>
        <small>Bienvenido(a), {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->rol) }})</small>
    </div>
    <div style="display:flex; gap:0.5rem;">
        <a href="{{ route('reportes.csv') }}" role="button" class="outline">Exportar CSV (Excel)</a>
        <a href="{{ route('reportes.pdf') }}" target="_blank" role="button">Reporte PDF / Imprimir</a>
    </div>
</header>

<div class="grid">
    <article><strong>Pacientes Activos</strong><h2>{{ $kpis['pacientes_activos'] }}</h2></article>
    <article><strong>Psicólogos Disponibles</strong><h2>{{ $kpis['psicologos_activos'] }}</h2></article>
    <article><strong>Citas Hoy</strong><h2>{{ $kpis['citas_hoy'] }}</h2></article>
    <article><strong>Sesiones Atendidas</strong><h2>{{ $kpis['citas_atendidas'] }}</h2></article>
    <article><strong>Evaluaciones Clínicas</strong><h2>{{ $kpis['evaluaciones_total'] }}</h2></article>
</div>

<article>
    <header><strong>Próximas Citas Programadas</strong></header>
    <table>
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Paciente</th>
                <th>Psicólogo</th>
                <th>Modalidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proximasCitas as $c)
            <tr>
                <td>{{ $c->fecha_hora->format('d/m/Y H:i') }}</td>
                <td>{{ $c->paciente->nombreCompleto() }}</td>
                <td>{{ $c->psicologo->nombreCompleto() }}</td>
                <td>{{ ucfirst($c->modalidad) }}</td>
                <td><span class="badge badge-warn">{{ strtoupper($c->estado) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5">No hay citas pendientes próximas.</td></tr>
            @endforelse
        </tbody>
    </table>
</article>
@endsection