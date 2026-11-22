<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Centro Psicológico Integral')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        :root { --pico-font-size: 95%; }
        nav.top-nav { border-bottom: 1px solid #e2e8f0; margin-bottom: 1.5rem; padding: 0.5rem 0; }
        .badge { padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-warn { background: #fef9c3; color: #854d0e; }
        .badge-off { background: #fee2e2; color: #991b1b; }
        .alert-ok { background: #dcfce7; border-left: 4px solid #16a34a; padding: 0.8rem 1rem; margin-bottom: 1rem; color: #14532d; }
        .alert-err { background: #fee2e2; border-left: 4px solid #dc2626; padding: 0.8rem 1rem; margin-bottom: 1rem; color: #7f1d1d; }
    </style>
</head>
<body>
    @auth
    <nav class="container top-nav">
        <ul>
            <li><strong>Centro Psicológico Integral</strong></li>
        </ul>
        <ul>
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><a href="{{ route('pacientes.index') }}">Pacientes</a></li> 
            <li><a href="{{ route('psicologos.index') }}">Psicólogos</a></li> 
            <li><a href="{{ route('citas.index') }}">Agenda de Citas</a></li>
            @if(auth()->user()->esAdmin())
                <li><a href="{{ route('usuarios.index') }}">Personal</a></li>
            @endif
            <li>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="outline secondary" style="padding:0.3rem 0.7rem;">
                        Salir ({{ auth()->user()->rol }})
                    </button>
                </form>
            </li>
        </ul>
    </nav>
    @endauth

    <main class="container">
        @if(session('exito'))
            <div class="alert-ok">{{ session('exito') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-err">
                <ul style="margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('contenido')
    </main>
</body>
</html>