@extends('layouts.app')
@section('titulo', 'Panel Principal · Centro Psicológico Integral')

@section('contenido')
<article>
    <header>
        <h3 style="margin:0;">Bienvenido(a), {{ auth()->user()->name }}</h3>
        <small>Rol institucional: <strong>{{ strtoupper(auth()->user()->rol) }}</strong> · DNI: {{ auth()->user()->dni }}</small>
    </header>
    <p>Sesión iniciada correctamente bajo control de acceso basado en roles (RBAC).</p>
</article>
@endsection