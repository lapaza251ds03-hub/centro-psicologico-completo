@extends('layouts.app')
@section('titulo', 'Iniciar Sesión · Centro Psicológico Integral')

@section('contenido')
<article style="max-width: 440px; margin: 3rem auto;">
    <header>
        <h3 style="margin:0;">Centro Psicológico Integral</h3>
        <small>Acceso exclusivo para personal autorizado</small>
    </header>
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <label>Correo institucional
            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="usuario@centropsicologico.pe">
        </label>
        <label>Contraseña
            <input type="password" name="password" required>
        </label>
        <button type="submit">Ingresar al Sistema</button>
    </form>
    <footer>
        <small>¿Eres psicólogo o recepcionista nuevo? <a href="{{ route('solicitud.form') }}">Solicitar habilitación de cuenta</a></small>
    </footer>
</article>
@endsection