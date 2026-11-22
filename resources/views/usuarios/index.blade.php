@extends('layouts.app')
@section('titulo', 'Gestión de Personal · Administración')

@section('contenido')
<h2>Gestión de Personal y Solicitudes (RF02)</h2>

@if($pendientes->isNotEmpty())
<article>
    <header><strong>Solicitudes Pendientes de Aprobación ({{ $pendientes->count() }})</strong></header>
    <table>
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol Solicitado y Aprobación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendientes as $p)
            <tr>
                <td>{{ $p->dni }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->email }}</td>
                <td>
                    <form action="{{ route('usuarios.aprobar', $p) }}" method="POST" style="display:flex; gap:0.5rem; margin:0;">
                        @csrf @method('PATCH')
                        <select name="rol" style="margin:0; width:auto;">
                            <option value="psicologo" @selected($p->rol === 'psicologo')>Psicólogo</option>
                            <option value="recepcionista" @selected($p->rol === 'recepcionista')>Recepcionista</option>
                            <option value="admin">Administrador</option>
                        </select>
                        <button type="submit" style="margin:0; padding:0.4rem 0.8rem;">Aprobar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</article>
@endif

<details>
    <summary role="button" class="outline">+ Registrar Nuevo Trabajador Directamente</summary>
    <article>
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf
            <div class="grid">
                <label>DNI <input type="text" name="dni" maxlength="8" required></label>
                <label>Nombre completo <input type="text" name="name" required></label>
                <label>Correo <input type="email" name="email" required></label>
            </div>
            <div class="grid">
                <label>Rol
                    <select name="rol" required>
                        <option value="psicologo">Psicólogo</option>
                        <option value="recepcionista">Recepcionista</option>
                        <option value="admin">Administrador</option>
                    </select>
                </label>
                <label>Contraseña inicial <input type="password" name="password" minlength="8" required></label>
            </div>
            <button type="submit">Guardar y Activar</button>
        </form>
    </article>
</details>

<table>
    <thead>
        <tr>
            <th>DNI</th>
            <th>Personal</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($activos as $u)
        <tr>
            <td>{{ $u->dni }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ ucfirst($u->rol) }}</td>
            <td>
                <span class="badge {{ $u->estado === 'activo' ? 'badge-ok' : 'badge-off' }}">
                    {{ strtoupper($u->estado) }}
                </span>
            </td>
            <td>
                @if($u->id !== auth()->id())
                <form action="{{ route('usuarios.estado', $u) }}" method="POST" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="outline secondary" style="padding:0.25rem 0.6rem; margin:0;">
                        {{ $u->estado === 'activo' ? 'Desactivar' : 'Reactivar' }}
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection