<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Institucional de Atenciones · Centro Psicológico Integral</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1a202c; margin: 20px; }
        h1 { font-size: 18px; margin-bottom: 4px; color: #1a365d; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e0; padding: 6px 8px; text-align: left; }
        th { background-color: #edf2f7; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="padding:8px 14px; margin-bottom:12px; cursor:pointer;">Imprimir / Guardar como PDF</button>
    <h1>Centro Psicológico Integral — Reporte Consolidado de Citas (RF15)</h1>
    <p>Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha y Horario</th>
                <th>Paciente (DNI)</th>
                <th>Psicólogo Tratante</th>
                <th>Modalidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($citas as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->fecha_hora->format('d/m/Y H:i') }} - {{ $c->fecha_hora_fin->format('H:i') }}</td>
                <td>{{ $c->paciente->nombreCompleto() }} ({{ $c->paciente->dni }})</td>
                <td>{{ $c->psicologo->nombreCompleto() }}</td>
                <td>{{ ucfirst($c->modalidad) }}</td>
                <td>{{ strtoupper($c->estado) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>