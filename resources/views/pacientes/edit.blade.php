@extends('layouts.app')
@section('titulo', 'Editar Paciente · Centro Psicológico Integral')

@section('contenido')
<article style="max-width: 720px; margin: 0 auto;">
    <header><strong>Editar Ficha de Paciente: {{ $paciente->nombreCompleto() }}</strong></header>
    <form action="{{ route('pacientes.update', $paciente) }}" method="POST">
        @csrf @method('PUT')
        <div class="grid">
            <label>DNI <input type="text" name="dni" maxlength="8" value="{{ old('dni', $paciente->dni) }}" required></label>
            <label>Nombres <input type="text" name="nombres" value="{{ old('nombres', $paciente->nombres) }}" required></label>
            <label>Apellidos <input type="text" name="apellidos" value="{{ old('apellidos', $paciente->apellidos) }}" required></label>
        </div>
        <div class="grid">
            <label>Fecha de Nacimiento <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento->format('Y-m-d')) }}" required></label>
            <label>Sexo
                <select name="sexo" required>
                    <option value="F" @selected($paciente->sexo === 'F')>Femenino</option>
                    <option value="M" @selected($paciente->sexo === 'M')>Masculino</option>
                </select>
            </label>
            <label>Teléfono <input type="text" name="telefono" value="{{ old('telefono', $paciente->telefono) }}"></label>
        </div>
        <label>Motivo de Consulta
            <textarea name="motivo_consulta" rows="2" required>{{ old('motivo_consulta', $paciente->motivo_consulta) }}</textarea>
        </label>
        <div class="grid">
            <label>Nombre Apoderado <input type="text" name="apoderado_nombre" value="{{ old('apoderado_nombre', $paciente->apoderado_nombre) }}"></label>
            <label>DNI Apoderado <input type="text" name="apoderado_dni" maxlength="8" value="{{ old('apoderado_dni', $paciente->apoderado_dni) }}"></label>
        </div>
        <label>
            <input type="checkbox" name="consentimiento_informado" value="1" @checked(old('consentimiento_informado', $paciente->consentimiento_informado))>
            Consentimiento informado firmado
        </label>
        <div class="grid">
            <a href="{{ route('pacientes.index') }}" role="button" class="outline secondary">Cancelar</a>
            <button type="submit">Actualizar Ficha</button>
        </div>
    </form>
</article>
@endsection