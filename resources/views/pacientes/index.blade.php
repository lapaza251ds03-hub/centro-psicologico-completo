@extends('layouts.app')
@section('titulo', 'Pacientes · Centro Psicológico Integral')

@section('contenido')
<h2>Gestión de Pacientes (RF04 – RF06)</h2>

<details>
    <summary role="button">+ Registrar Nuevo Paciente</summary>
    <article>
        <form action="{{ route('pacientes.store') }}" method="POST">
            @csrf
            <div class="grid">
                <label>DNI (8 dígitos) <input type="text" name="dni" maxlength="8" pattern="\d{8}" value="{{ old('dni') }}" required></label>
                <label>Nombres <input type="text" name="nombres" value="{{ old('nombres') }}" required></label>
                <label>Apellidos <input type="text" name="apellidos" value="{{ old('apellidos') }}" required></label>
            </div>
            <div class="grid">
                <label>Fecha de Nacimiento <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required></label>
                <label>Sexo
                    <select name="sexo" required>
                        <option value="F">Femenino</option>
                        <option value="M">Masculino</option>
                    </select>
                </label>
                <label>Teléfono <input type="text" name="telefono" maxlength="15" value="{{ old('telefono') }}"></label>
            </div>
            <label>Motivo de Consulta
                <textarea name="motivo_consulta" rows="2" required>{{ old('motivo_consulta') }}</textarea>
            </label>
            <fieldset>
                <legend><strong>Datos del Apoderado (Obligatorio si es menor de 18 años - RN03)</strong></legend>
                <div class="grid">
                    <label>Nombre completo del apoderado <input type="text" name="apoderado_nombre" value="{{ old('apoderado_nombre') }}"></label>
                    <label>DNI del apoderado <input type="text" name="apoderado_dni" maxlength="8" value="{{ old('apoderado_dni') }}"></label>
                </div>
                <label>
                    <input type="checkbox" name="consentimiento_informado" value="1" @checked(old('consentimiento_informado'))>
                    Cuenta con Consentimiento Informado firmado
                </label>
            </fieldset>
            <button type="submit">Guardar Paciente</button>
        </form>
    </article>
</details>

<form method="GET" action="{{ route('pacientes.index') }}" role="search" style="margin-top:1rem;">
    <input type="search" name="q" value="{{ $busqueda }}" placeholder="Buscar por DNI, nombres o apellidos...">
    <button type="submit">Buscar</button>
</form>

<table>
    <thead>
        <tr>
            <th>DNI</th>
            <th>Paciente</th>
            <th>Edad / Grupo Etario</th>
            <th>Apoderado</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pacientes as $p)
        <tr>
            <td>{{ $p->dni }}</td>
            <td><strong>{{ $p->nombreCompleto() }}</strong><br><small>{{ Str::limit($p->motivo_consulta, 45) }}</small></td>
            <td>{{ $p->fecha_nacimiento->age }} años<br><small>({{ $p->grupoEtario() }})</small></td>
            <td>{{ $p->esMenorDeEdad() ? "{$p->apoderado_nombre} (DNI: {$p->apoderado_dni})" : 'Mayor de edad' }}</td>
            <td><span class="badge {{ $p->activo ? 'badge-ok' : 'badge-off' }}">{{ $p->activo ? 'ACTIVO' : 'INACTIVO' }}</span></td>
            <td style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                <a href="{{ route('pacientes.edit', $p) }}" role="button" class="outline" style="padding:0.25rem 0.55rem;">Editar</a>
                @if(!auth()->user()->esRecepcionista())
                    <a href="{{ route('evaluaciones.historial', $p) }}" role="button" class="secondary" style="padding:0.25rem 0.55rem;">Historial Clínico</a>
                @endif
                <form action="{{ route('pacientes.estado', $p) }}" method="POST" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="outline secondary" style="padding:0.25rem 0.55rem; margin:0;">
                        {{ $p->activo ? 'Inactivar' : 'Activar' }}
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection