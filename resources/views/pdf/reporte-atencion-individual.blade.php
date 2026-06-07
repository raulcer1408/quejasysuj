<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:DejaVu Sans,sans-serif; font-size:8px; color:#1a1a1a; padding:15px 20px; }

        .header { width:100%; border-collapse:collapse; margin-bottom:10px; }
        .header td { border:1px solid #000; vertical-align:middle; }
        .cell-logo { width:10%; text-align:center; padding:4px; background:#fff; }
        .cell-logo img { max-width:100%; max-height:50px; }
        .cell-titulo { width:90%; text-align:center; padding:4px 8px; }
        .cell-titulo .inst { font-size:11px; font-weight:bold; color:#c8102e; text-transform:uppercase; }
        .cell-titulo .sub  { font-size:9px; font-weight:bold; margin-top:2px; }
        .cell-titulo .per  { font-size:7px; color:#666; margin-top:2px; }

        .bloque-titulo { background:#c8102e; color:#fff; font-weight:bold; font-size:8px;
                         padding:3px 6px; text-transform:uppercase; margin-bottom:0; }

        table.datos { width:100%; border-collapse:collapse; font-size:7.5px; margin-bottom:10px; }
        table.datos th { background:#f4f6f9; font-weight:bold; padding:2px 4px;
                         border:1px solid #dee2e6; text-align:center; }
        table.datos td { padding:2px 4px; border:1px solid #dee2e6; }
        table.datos td.c { text-align:center; }
        table.datos tr:nth-child(even) { background:#fafafa; }

        .verde   { color:#28a745; font-weight:bold; }
        .naranja { color:#e0a800; font-weight:bold; }
        .rojo    { color:#dc3545; font-weight:bold; }

        .footer { border-top:1px solid #dee2e6; margin-top:10px; padding-top:4px;
                  font-size:7px; color:#888; text-align:center; }
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
                <div class="sub">Reporte de Atención Individual por Actor</div>
                <div class="per">
                    Período: {{ ($filtros['desde'] || $filtros['hasta']) ? (($filtros['desde'] ?? '—').' al '.($filtros['hasta'] ?? '—')) : 'Todo el período' }}
                    &nbsp;|&nbsp; Generado: {{ now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Resumen por actor --}}
    <div class="bloque-titulo">Resumen por Actor</div>
    <table class="datos">
        <tr>
            <th>Actor</th><th>Rol(es)</th><th>Intervenciones</th>
            <th>Mín. (h)</th><th>Máx. (h)</th><th>Promedio (h)</th><th>Promedio (días)</th>
        </tr>
        @foreach ($resumen as $r)
        <tr>
            <td><strong>{{ $r['user']->name }}</strong></td>
            <td>{{ implode(', ', $r['roles']) }}</td>
            <td class="c">{{ $r['intervenciones'] }}</td>
            <td class="c">{{ $r['horas_min'] }}</td>
            <td class="c">{{ $r['horas_max'] }}</td>
            <td class="c">
                @php $d = $r['promedio_dias']; @endphp
                <span class="{{ $d <= 1 ? 'verde' : ($d <= 3 ? 'naranja' : 'rojo') }}">
                    {{ $r['promedio_horas'] }}h
                </span>
            </td>
            <td class="c">
                <span class="{{ $d <= 1 ? 'verde' : ($d <= 3 ? 'naranja' : 'rojo') }}">
                    {{ $r['promedio_dias'] }}d
                </span>
            </td>
        </tr>
        @endforeach
    </table>

    {{-- Detalle --}}
    <div class="bloque-titulo">Detalle de Intervenciones</div>
    <table class="datos">
        <tr>
            <th>Actor</th><th>Rol</th><th>#</th><th>Servicio</th>
            <th>Tipo</th><th>Recibió</th><th>Respondió</th><th>Horas</th><th>Días</th>
        </tr>
        @foreach ($intervenciones as $item)
            @php $d = $item['dias']; @endphp
            <tr>
                <td>{{ $item['user']->name }}</td>
                <td class="c">{{ $item['rol'] }}</td>
                <td class="c">{{ str_pad($item['queja']->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ \App\Models\Queja::SERVICIOS[$item['queja']->servicio] ?? $item['queja']->servicio }}</td>
                <td>{{ \App\Models\Queja::TIPOS[$item['queja']->tipo_solicitud] ?? $item['queja']->tipo_solicitud }}</td>
                <td class="c">{{ $item['recibido_en']->format('d/m/Y H:i') }}</td>
                <td class="c">{{ $item['respondio_en']->format('d/m/Y H:i') }}</td>
                <td class="c"><span class="{{ $d <= 1 ? 'verde' : ($d <= 3 ? 'naranja' : 'rojo') }}">{{ $item['horas'] }}</span></td>
                <td class="c"><span class="{{ $d <= 1 ? 'verde' : ($d <= 3 ? 'naranja' : 'rojo') }}">{{ $d }}</span></td>
            </tr>
        @endforeach
    </table>

    <div class="footer">
        Escuela de Jueces del Estado — Sistema de Quejas y Sugerencias de Mejora &nbsp;|&nbsp;
        Reporte de atención individual generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
        &nbsp;|&nbsp;
        <span style="color:#28a745;">■</span> ≤ 1d rápido &nbsp;
        <span style="color:#e0a800;">■</span> ≤ 3d moderado &nbsp;
        <span style="color:#dc3545;">■</span> > 3d tardío
    </div>

</body>
</html>
