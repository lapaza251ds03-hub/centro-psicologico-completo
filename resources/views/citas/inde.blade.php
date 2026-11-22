@extends('layouts.app')
@section('titulo', 'Agenda de Citas · Centro Psicológico Integral')

@section('contenido')
<h2>Agenda de Citas Psicológicas (RF09 – RF11)</h2>

<details open>
    <summary role="button">+ Agendar Nueva Sesión (Bloque fijo 60 min - RN06)</summary>
    <article>
        <form action="{{ route('citas.store') }}" method="POST">
            @csrf
            <div class="grid">
                <label>Paciente
                    <select name="paciente_id" required>
                        <option value="">-- Seleccionar paciente --</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" @selected(old('paciente_id') == $p->id)>
                                {{ $p->nombreCompleto() }} ({{ $p->fecha_nacimiento->age }} años · {{ $p->grupoEtario() }})
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>Psicólogo Disponible (RN04 & RN05)
                    <select name="psicologo_id" required>
                        <option value="">-- Seleccionar psicólogo según grupo etario --</option>
                        @foreach($psicologos as $ps)
                            <option value="{{ $ps->id }}" @selected(old('psicologo_id') == $ps->id)>
                                {{ $ps->nombreCompleto() }} · {{ $ps->especialidad }} [{{ $ps->grupo_etario }}]
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="grid">
                <label>Fecha y Hora de Inicio
                    <input type="datetime-local" name="fecha_hora" value="{{ old('fecha_hora') }}" required>
                </label>
                <label>Modalidad
                    <select name="modalidad" required>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </label>
                <label>Tipo de Sesión
                    <input type="text" name="tipo_sesion" value="{{ old('tipo_sesion', 'Evaluación inicial') }}" required>
                </label>
            </div>
            <button type="submit">Programar Cita (60 min)</button>
        </form>
    </article>
</details>

<table>
    <thead>
        <tr>
            <th>Horario (60 min)</th>
            <th>Paciente</th>
            <th>Psicólogo</th>
            <th>Modalidad / Sesión</th>
            <th>Estado</th>
            <th>Gestión</th>
        </tr>
    </thead>
    <tbody>
        @foreach($citas as $c)
        <tr>
            <td><strong>{{ $c->fecha_hora->format('d/m/Y') }}</strong><br>{{ $c->fecha_hora->format('H:i') }} – {{ $c->fecha_hora_fin->format('H:i') }}</td>
            <td>{{ $c->paciente->nombreCompleto() }}<br><small>DNI: {{ $c->paciente->dni }}</small></td>
            <td>{{ $c->psicologo->nombreCompleto() }}<br><small>{{ $c->psicologo->especialidad }}</small></td>
            <td>{{ ucfirst($c->modalidad) }}<br><small>{{ $c->tipo_sesion }}</small></td>
            <td>
                <span class="badge {{ $c->estado === 'atendida' ? 'badge-ok' : ($c->estado === 'cancelada' ? 'badge-off' : 'badge-warn') }}">
                    {{ strtoupper($c->estado) }}
                </span>
            </td>
            <td>
                <form action="{{ route('citas.estado', $c) }}" method="POST" style="display:flex; gap:0.35rem; margin:0 0 0.4rem 0;">
                    @csrf @method('PATCH')
                    <select name="estado" style="margin:0; padding:0.2rem; width:auto;">
                        @foreach(['pendiente', 'confirmada', 'atendida', 'cancelada'] as $est)
                            <option value="{{ $est }}" @selected($c->estado === $est)>{{ ucfirst($est) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="outline" style="padding:0.2rem 0.5rem; margin:0;">OK</button>
                </form>

                @if($c->estado === 'atendida' && !auth()->user()->esRecepcionista())
                    @if($c->evaluacionClinica)
                        <a href="{{ route('evaluaciones.historial', $c->paciente_id) }}" role="button" class="secondary" style="padding:0.2rem 0.5rem; font-size:0.8rem;">Ver Evaluación</a>
                    @else
                        <a href="{{ route('evaluaciones.create', $c) }}" role="button" style="padding:0.2rem 0.5rem; font-size:0.8rem;">+ Evaluación Clínica</a>
                    @endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection