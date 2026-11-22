@extends('layouts.app')
@section('titulo', 'Solicitud de Acceso · Centro Psicológico Integral')

@section('contenido')
<article style="max-width: 520px; margin: 2rem auto;">
    <header>
        <h3 style="margin:0;">Solicitud de Acceso al Sistema (RF01)</h3>
        <small>Tu cuenta quedará pendiente hasta que Dirección valide tu DNI y rol.</small>
    </header>
    <form method="POST" action="{{ route('solicitud.store') }}">
        @csrf
        <div class="grid">
            <label>DNI (8 dígitos)
                <input type="text" name="dni" maxlength="8" pattern="\d{8}" value="{{ old('dni') }}" required>
            </label>
            <label>Rol solicitado
                <select name="rol" required>
                    <option value="psicologo">Psicólogo(a)</option>
                    <option value="recepcionista">Recepcionista</option>
                </select>
            </label>
        </div>
        <label>Nombres y Apellidos completos
            <input type="text" name="name" value="{{ old('name') }}" required>
        </label>
        <label>Correo electrónico
            <input type="email" name="email" value="{{ old('email') }}" required>
        </label>
        <div class="grid">
            <label>Contraseña (mín. 8 caracteres)
                <input type="password" name="password" minlength="8" required>
            </label>
            <label>Confirmar contraseña
                <input type="password" name="password_confirmation" minlength="8" required>
            </label>
        </div>
        <button type="submit">Enviar Solicitud</button>
    </form>
    <footer>
        <a href="{{ route('login') }}">← Volver al inicio de sesión</a>
    </footer>
</article>
@endsection