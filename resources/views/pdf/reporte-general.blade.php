<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 20px 25px; }

        .header { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .header td { border: 1px solid #000; vertical-align: middle; }
        .cell-logo { width: 12%; text-align: center; padding: 5px; background-color: #fff; }
        .cell-logo img { max-width: 100%; max-height: 60px; }
        .cell-titulo { width: 60%; text-align: center; padding: 6px 10px; }
        .cell-titulo .inst { font-size: 13px; font-weight: bold; color: #c8102e; text-transform: uppercase; }
        .cell-titulo .doc-name { font-size: 11px; font-weight: bold; margin-top: 4px; }
        .cell-meta { width: 28%; padding: 0; }
        .meta-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .meta-table td { border: 1px solid #000; padding: 4px 6px; }
        .meta-table .meta-label { font-weight: bold; width: 55%; }
        .meta-table .meta-value { text-align: center; }

        .filtros { background: #f8f9fa; border: 1px solid #dee2e6; border-left: 4px solid #c8102e;
                   padding: 7px 10px; margin-bottom: 10px; font-size: 10px; }
        .filtros strong { color: #c8102e; }

        table.datos { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.datos th { background-color: #c8102e; color: #fff; padding: 5px 6px; text-align: center;
                         border: 1px solid #aaa; font-size: 10px; }
        table.datos td { padding: 4px 6px; border: 1px solid #dee2e6; vertical-align: top; }
        table.datos tr:nth-child(even) { background-color: #f8f9fa; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px;
                 font-weight: bold; color: #fff; }
        .badge-danger  { background-color: #dc3545; }
        .badge-info    { background-color: #17a2b8; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-primary { background-color: #007bff; }
        .badge-secondary { background-color: #6c757d; }
        .badge-purple  { background-color: #6f42c1; }

        .footer { border-top: 1px solid #dee2e6; margin-top: 16px; padding-top: 6px;
                  font-size: 9px; color: #888; text-align: center; }

        .total-row { background-color: #fff3cd; font-weight: bold; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="cell-logo">
                <img src="{{ public_path('vendor/adminlte/dist/img/logoeje.png') }}">
            </td>
            <td class="cell-titulo">
                <div class="inst">Escuela de Jueces del Estado</div>
                <div class="doc-name">Reporte General de Quejas y Sugerencias de Mejora</div>
            </td>
            <td class="cell-meta">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Generado</td>
                        <td class="meta-value">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Total registros</td>
                        <td class="meta-value">{{ $quejas->count() }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Filtros aplicados --}}
    <div class="filtros">
        <strong>Filtros aplicados:</strong>
        Estado: {{ $filtros['estado'] ? (\App\Models\Queja::ESTADOS[$filtros['estado']] ?? $filtros['estado']) : 'Todos' }} |
        Servicio: {{ $filtros['servicio'] ? (\App\Models\Queja::SERVICIOS[$filtros['servicio']] ?? $filtros['servicio']) : 'Todos' }} |
        Tipo: {{ $filtros['tipo'] ? (\App\Models\Queja::TIPOS[$filtros['tipo']] ?? $filtros['tipo']) : 'Todos' }} |
        Departamento: {{ $filtros['departamento'] ? (\App\Models\User::DEPARTAMENTOS[$filtros['departamento']] ?? $filtros['departamento']) : 'Todos' }} |
        Período: {{ $filtros['desde'] ? $filtros['desde'] : '—' }} al {{ $filtros['hasta'] ? $filtros['hasta'] : '—' }}
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:7%">Tipo</th>
                <th style="width:9%">Servicio</th>
                <th style="width:18%">Actividad</th>
                <th style="width:13%">Solicitante</th>
                <th style="width:9%">Departamento</th>
                <th style="width:10%">Estado</th>
                <th style="width:11%">Revisor</th>
                <th style="width:11%">Jefe</th>
                <th style="width:7%">Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quejas as $queja)
                @php
                    $colores = [
                        'pendiente'            => 'warning',
                        'en_revision'          => 'info',
                        'derivado_coordinador' => 'primary',
                        'respondido'           => 'secondary',
                        'en_validacion'        => 'purple',
                        'resuelto'             => 'success',
                        'no_procede'           => 'danger',
                    ];
                @endphp
                <tr>
                    <td style="text-align:center;font-weight:bold;">{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $queja->tipo_solicitud === 'queja' ? 'danger' : 'info' }}">
                            {{ \App\Models\Queja::TIPOS[$queja->tipo_solicitud] ?? $queja->tipo_solicitud }}
                        </span>
                    </td>
                    <td>{{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</td>
                    <td>{{ Str::limit($queja->nombre_actividad, 40) }}</td>
                    <td>{{ $queja->user->name }}</td>
                    <td>{{ \App\Models\User::DEPARTAMENTOS[$queja->user->departamento ?? ''] ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $colores[$queja->estado] ?? 'secondary' }}">
                            {{ \App\Models\Queja::ESTADOS[$queja->estado] ?? $queja->estado }}
                        </span>
                    </td>
                    <td>{{ $queja->revisor?->name ?? '—' }}</td>
                    <td>{{ $queja->jefe?->name ?? '—' }}</td>
                    <td style="text-align:center;">{{ $queja->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Escuela de Jueces del Estado — Sistema de Quejas y Sugerencias de Mejora &nbsp;|&nbsp;
        Reporte generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
    </div>

</body>
</html>
