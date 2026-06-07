<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:DejaVu Sans,sans-serif; font-size:10px; color:#1a1a1a; padding:15px 20px; }

        .header { width:100%; border-collapse:collapse; margin-bottom:10px; }
        .header td { border:1px solid #000; vertical-align:middle; }
        .cell-logo { width:10%; text-align:center; padding:4px; background:#fff; }
        .cell-logo img { max-width:100%; max-height:50px; }
        .cell-titulo { width:90%; text-align:center; padding:4px 8px; }
        .cell-titulo .inst { font-size:13px; font-weight:bold; color:#c8102e; text-transform:uppercase; }
        .cell-titulo .sub  { font-size:10.5px; font-weight:bold; margin-top:2px; }
        .cell-titulo .per  { font-size:9px; color:#666; margin-top:2px; }

        .info-boxes { width:100%; border-collapse:collapse; margin-bottom:8px; }
        .info-box td { text-align:center; border:1px solid #dee2e6; border-top:3px solid #c8102e;
                       padding:5px; width:25%; }
        .info-box .num { font-size:17px; font-weight:bold; color:#c8102e; }
        .info-box .lbl { font-size:8.5px; color:#666; }

        .bloque-titulo { background:#c8102e; color:#fff; font-weight:bold; font-size:9.5px;
                         padding:3px 6px; text-transform:uppercase; }
        table.datos { width:100%; border-collapse:collapse; font-size:9px; margin-bottom:8px; }
        table.datos th { background:#f4f6f9; font-weight:bold; padding:3px 5px;
                         border:1px solid #dee2e6; text-align:center; }
        table.datos td { padding:3px 5px; border:1px solid #dee2e6; }
        table.datos td.c { text-align:center; }
        table.datos tr.lenta { background:#fff3cd; }
        table.datos tr:nth-child(even):not(.lenta) { background:#fafafa; }

        .barra { display:inline-block; height:7px; border-radius:2px; }
        .fila2 { width:100%; border-collapse:collapse; margin-bottom:8px; }
        .fila2 td { vertical-align:top; padding:0 3px; }

        .footer { border-top:1px solid #dee2e6; margin-top:10px; padding-top:4px;
                  font-size:8.5px; color:#888; text-align:center; }
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
                <div class="sub">Reporte de Tiempos de Atención — Quejas y Sugerencias de Mejora</div>
                <div class="per">
                    Período: {{ ($filtros['desde'] || $filtros['hasta']) ? (($filtros['desde'] ?? '—').' al '.($filtros['hasta'] ?? '—')) : 'Todo el período' }}
                    &nbsp;|&nbsp; Umbral solicitudes lentas: {{ $filtros['umbral'] }} días
                    &nbsp;|&nbsp; Generado: {{ now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Info boxes --}}
    <table class="info-boxes">
        <tr class="info-box">
            <td><div class="num">{{ $datos['total'] }}</div><div class="lbl">Solicitudes cerradas</div></td>
            <td><div class="num">{{ $datos['promedio'] }}d</div><div class="lbl">Promedio atención</div></td>
            <td><div class="num">{{ $datos['minimo'] }}d</div><div class="lbl">Más rápida</div></td>
            <td><div class="num" style="color:#dc3545;">{{ $datos['lentas'] }}</div><div class="lbl">Lentas (> {{ $filtros['umbral'] }}d)</div></td>
        </tr>
    </table>

    {{-- Promedios por servicio y revisor --}}
    <table class="fila2">
        <tr>
            <td style="width:35%;">
                <div class="bloque-titulo">Promedio por Servicio</div>
                <table class="datos">
                    <tr><th>Servicio</th><th>Promedio</th><th style="width:35%;">Barra</th></tr>
                    @php $maxS = !empty($datos['porServicio']) ? max($datos['porServicio']) : 1; @endphp
                    @foreach ($datos['porServicio'] as $s => $p)
                    <tr>
                        <td>{{ $s }}</td>
                        <td class="c">{{ $p }}d</td>
                        <td><span class="barra" style="width:{{ $maxS>0?round(($p/$maxS)*70):0 }}px;background:{{ $p > $filtros['umbral'] ? '#dc3545' : '#007bff' }};"></span></td>
                    </tr>
                    @endforeach
                </table>
            </td>
            <td style="width:65%;">
                <div class="bloque-titulo">Promedio por Revisor</div>
                <table class="datos">
                    <tr><th>Revisor</th><th>Promedio</th><th style="width:40%;">Barra</th></tr>
                    @php $maxR = !empty($datos['porRevisor']) ? max($datos['porRevisor']) : 1; @endphp
                    @foreach ($datos['porRevisor'] as $r => $p)
                    <tr>
                        <td>{{ $r }}</td>
                        <td class="c">{{ $p }}d</td>
                        <td><span class="barra" style="width:{{ $maxR>0?round(($p/$maxR)*100):0 }}px;background:{{ $p > $filtros['umbral'] ? '#dc3545' : '#28a745' }};"></span></td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- Detalle --}}
    <div class="bloque-titulo">Detalle por Solicitud</div>
    <table class="datos">
        <tr>
            <th>#</th><th>Tipo</th><th>Servicio</th><th>Solicitante</th>
            <th>Revisor</th><th>Estado</th><th>Registro</th><th>Cierre</th><th>Total días</th>
        </tr>
        @foreach ($datos['filas'] as $f)
            @php $q = $f['queja']; $lenta = $f['total'] !== null && $f['total'] > $filtros['umbral']; @endphp
            <tr class="{{ $lenta ? 'lenta' : '' }}">
                <td class="c">{{ str_pad($q->id,5,'0',STR_PAD_LEFT) }}</td>
                <td>{{ \App\Models\Queja::TIPOS[$q->tipo_solicitud] ?? $q->tipo_solicitud }}</td>
                <td>{{ \App\Models\Queja::SERVICIOS[$q->servicio] ?? $q->servicio }}</td>
                <td>{{ $q->user->name }}</td>
                <td>{{ $q->revisor?->name ?? '—' }}</td>
                <td>{{ \App\Models\Queja::ESTADOS[$q->estado] ?? $q->estado }}</td>
                <td class="c">{{ $q->created_at->format('d/m/Y') }}</td>
                <td class="c">{{ $f['cierre']?->format('d/m/Y') ?? '—' }}</td>
                <td class="c" style="font-weight:bold;color:{{ $f['total'] !== null && $f['total'] > $filtros['umbral'] ? '#dc3545' : '#28a745' }};">
                    {{ $f['total'] !== null ? $f['total'].'d' : '—' }}{{ $lenta ? ' ⚠' : '' }}
                </td>
            </tr>
        @endforeach
    </table>

    <div class="footer">
        Escuela de Jueces del Estado — Sistema de Quejas y Sugerencias de Mejora &nbsp;|&nbsp;
        Reporte de tiempos generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
        &nbsp;|&nbsp; ⚠ = Solicitud que excedió el umbral de {{ $filtros['umbral'] }} días
    </div>

</body>
</html>
