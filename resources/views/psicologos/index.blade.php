@extends('layouts.app')
@section('titulo', 'Equipo Terapéutico · Centro Psicológico Integral')

@section('contenido')
<h2>Equipo de Psicólogos y Especialidades (RF07)</h2>

@if(auth()->user()->esAdmin())
<details>
    <summary role="button">+ Registrar Nuevo Psicólogo</summary>
    <article>
        <form action="{{ route('psicologos.store') }}" method="POST">
            @csrf
            <div class="grid">
                <label>DNI <input type="text" name="dni" maxlength="8" required></label>
                <label>Nombres <input type="text" name="nombres" required></label>
                <label>Apellidos <input type="text" name="apellidos" required></label>
            </div>
            <div class="grid">
                <label>Especialidad
                    <select name="especialidad" required>
                        <option value="Clínica">Clínica</option>
                        <option value="Infantil">Infantil</option>
                        <option value="Pareja y Familia">Pareja y Familia</option>
                        <option value="Neuropsicología">Neuropsicología</option>
                        <option value="Educativa">Educativa</option>
                    </select>
                </label>
                <label>Grupo Etario que Atiende (RN05)
                    <select name="grupo_etario" required>
                        <option value="ninos">Niños (0–11 años)</option>
                        <option value="adolescentes">Adolescentes (12–17 años)</option>
                        <option value="adultos">Adultos (18–59 años)</option>
                        <option value="adulto_mayor">Adulto Mayor (60+ años)</option>
                    </select>
                </label>
                <label>Colegiatura (C.Ps.P.) <input type="text" name="colegiatura" placeholder="C.Ps.P. 34512" required></label>
            </div>
            <label>Vincular a cuenta de usuario (opcional)
                <select name="user_id">
                    <option value="">-- Sin cuenta vinculada aún --</option>
                    @foreach($usuariosPsicologos as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </label>
            <button type="submit">Registrar Psicólogo</button>
        </form>
    </article>
</details>
@endif

<table>
    <thead>
        <tr>
            <th>Colegiatura</th>
            <th>Profesional</th>
            <th>Especialidad</th>
            <th>Grupo Etario (RN05)</th>
            <th>Disponibilidad (RN04)</th>
            @if(auth()->user()->esAdmin()) <th>Acción</th> @endif
        </tr>
    </thead>
    <tbody>
        @foreach($psicologos as $ps)
        <tr>
            <td><strong>{{ $ps->colegiatura }}</strong></td>
            <td>{{ $ps->nombreCompleto() }}<br><small>DNI: {{ $ps->dni }}</small></td>
            <td>{{ $ps->especialidad }}</td>
            <td>{{ $ps->etiquetaGrupoEtario() }}</td>
            <td><span class="badge {{ $ps->activo ? 'badge-ok' : 'badge-off' }}">{{ $ps->activo ? 'DISPONIBLE' : 'INACTIVO / LICENCIA' }}</span></td>
            @if(auth()->user()->esAdmin())
            <td>
                <form action="{{ route('psicologos.estado', $ps) }}" method="POST" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="outline secondary" style="padding:0.25rem 0.6rem; margin:0;">
                        {{ $ps->activo ? 'Poner en Licencia' : 'Habilitar' }}
                    </button>
                </form>
            </td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
@endsection