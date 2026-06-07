<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:9px; color:#1a1a1a; padding:20px 25px; }

        .header { width:100%; border-collapse:collapse; margin-bottom:10px; }
        .header td { border:1px solid #000; vertical-align:middle; }
        .cell-logo { width:12%; text-align:center; padding:4px; background:#fff; }
        .cell-logo img { max-width:100%; max-height:52px; }
        .cell-titulo { width:88%; text-align:center; padding:5px; }
        .cell-titulo .inst { font-size:12px; font-weight:bold; color:#c8102e; text-transform:uppercase; }
        .cell-titulo .sub  { font-size:10px; font-weight:bold; margin-top:3px; }
        .cell-titulo .per  { font-size:8px; color:#666; margin-top:2px; }

        .fila-bloques { width:100%; border-collapse:collapse; margin-bottom:10px; }
        .bloque { vertical-align:top; padding:0 4px; }

        .bloque-titulo {
            background:#c8102e; color:#fff; font-weight:bold; font-size:8.5px;
            padding:4px 7px; text-transform:uppercase; letter-spacing:.3px;
        }
        table.datos { width:100%; border-collapse:collapse; font-size:8px; }
        table.datos th { background:#f4f6f9; font-weight:bold; padding:3px 5px; border:1px solid #dee2e6; text-align:center; }
        table.datos td { padding:3px 5px; border:1px solid #dee2e6; }
        table.datos td.num { text-align:center; font-weight:bold; }
        table.datos td.pct { text-align:center; color:#666; }
        table.datos tr:nth-child(even) { background:#fafafa; }

        .info-box { display:inline-block; width:23%; text-align:center; border:1px solid #dee2e6;
                    border-top:4px solid #c8102e; padding:8px 4px; margin:0 .5%; border-radius:3px; }
        .info-box .num  { font-size:20px; font-weight:bold; color:#c8102e; }
        .info-box .txt  { font-size:7.5px; color:#666; }

        .progress-wrap { width:100%; background:#eee; border-radius:3px; height:10px; margin-top:4px; }
        .progress-bar  { height:10px; border-radius:3px; background:#28a745; display:inline-block; }
        .progress-bar2 { height:10px; border-radius:3px; background:#dc3545; display:inline-block; }

        .barra { display:inline-block; background:#c8102e; height:8px; border-radius:2px; }

        .footer { border-top:1px solid #dee2e6; margin-top:14px; padding-top:5px;
                  font-size:7.5px; color:#888; text-align:center; }
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
                <div class="sub">Reporte Estadístico — Quejas y Sugerencias de Mejora</div>
                <div class="per">
                    Período: {{ ($filtros['desde'] || $filtros['hasta']) ? (($filtros['desde'] ?? '—').' al '.($filtros['hasta'] ?? '—')) : 'Todo el período' }}
                    &nbsp;|&nbsp; Generado: {{ now()->format('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Info boxes --}}
    <div style="text-align:center; margin-bottom:10px;">
        <div class="info-box">
            <div class="num">{{ $stats['total'] }}</div>
            <div class="txt">Total Solicitudes</div>
        </div>
        <div class="info-box">
            <div class="num" style="color:#28a745;">{{ $stats['resueltas'] }}</div>
            <div class="txt">Resueltas</div>
        </div>
        <div class="info-box">
            <div class="num" style="color:#dc3545;">{{ $stats['noProcede'] }}</div>
            <div class="txt">No Procede</div>
        </div>
        <div class="info-box">
            <div class="num" style="color:#ffc107;">{{ $stats['enProceso'] }}</div>
            <div class="txt">En Proceso</div>
        </div>
    </div>

    {{-- Tablas por tipo / servicio / estado --}}
    <table class="fila-bloques">
        <tr>
            <td class="bloque" style="width:32%;">
                <div class="bloque-titulo">Por Tipo de Solicitud</div>
                <table class="datos">
                    <tr><th>Tipo</th><th>Total</th><th>%</th></tr>
                    @foreach ($stats['porTipo'] as $etiqueta => $total)
                    <tr>
                        <td>{{ $etiqueta }}</td>
                        <td class="num">{{ $total }}</td>
                        <td class="pct">{{ $stats['total'] > 0 ? round(($total/$stats['total'])*100,1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </table>
            </td>
            <td class="bloque" style="width:34%;">
                <div class="bloque-titulo">Por Servicio</div>
                <table class="datos">
                    <tr><th>Servicio</th><th>Total</th><th>%</th></tr>
                    @foreach ($stats['porServicio'] as $etiqueta => $total)
                    <tr>
                        <td>{{ $etiqueta }}</td>
                        <td class="num">{{ $total }}</td>
                        <td class="pct">{{ $stats['total'] > 0 ? round(($total/$stats['total'])*100,1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </table>
            </td>
            <td class="bloque" style="width:34%;">
                <div class="bloque-titulo">Por Estado</div>
                <table class="datos">
                    <tr><th>Estado</th><th>Total</th><th>%</th></tr>
                    @foreach ($stats['porEstado'] as $etiqueta => $total)
                    @if ($total > 0)
                    <tr>
                        <td>{{ $etiqueta }}</td>
                        <td class="num">{{ $total }}</td>
                        <td class="pct">{{ $stats['total'] > 0 ? round(($total/$stats['total'])*100,1) : 0 }}%</td>
                    </tr>
                    @endif
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- Departamento y tendencia --}}
    <table class="fila-bloques" style="margin-top:8px;">
        <tr>
            <td class="bloque" style="width:40%;">
                <div class="bloque-titulo">Por Departamento de Origen</div>
                @if (empty($stats['porDepartamento']))
                    <p style="font-size:8px;color:#999;padding:4px;">Sin datos de departamento.</p>
                @else
                <table class="datos">
                    <tr><th>Departamento</th><th>Total</th><th>%</th></tr>
                    @foreach ($stats['porDepartamento'] as $dep => $total)
                    <tr>
                        <td>{{ $dep }}</td>
                        <td class="num">{{ $total }}</td>
                        <td class="pct">{{ $stats['total'] > 0 ? round(($total/$stats['total'])*100,1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </table>
                @endif
            </td>
            <td class="bloque" style="width:60%;">
                <div class="bloque-titulo">Tendencia Mensual</div>
                @if ($stats['tendencia']->isEmpty())
                    <p style="font-size:8px;color:#999;padding:4px;">Sin datos en el período.</p>
                @else
                <table class="datos">
                    <tr><th>Mes</th><th>Total</th><th style="width:50%;">Barra</th></tr>
                    @php $maxMes = $stats['tendencia']->max('total'); @endphp
                    @foreach ($stats['tendencia'] as $t)
                    <tr>
                        <td>{{ $t['mes'] }}</td>
                        <td class="num">{{ $t['total'] }}</td>
                        <td>
                            @php $pct = $maxMes > 0 ? round(($t['total']/$maxMes)*100) : 0; @endphp
                            <span class="barra" style="width:{{ $pct }}%;"></span>
                        </td>
                    </tr>
                    @endforeach
                </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- Tasa de resolución --}}
    <div style="margin-top:10px; border:1px solid #dee2e6; border-left:4px solid #c8102e; padding:6px 10px;">
        <strong style="font-size:9px;">Tasa de Resolución: {{ $stats['tasaExito'] }}%</strong>
        <div class="progress-wrap" style="margin-top:4px;">
            <span class="progress-bar" style="width:{{ $stats['tasaExito'] }}%;"></span>@if($stats['total']>0)<span class="progress-bar2" style="width:{{ round(($stats['noProcede']/$stats['total'])*100,1) }}%;"></span>@endif
        </div>
        <div style="font-size:7.5px;color:#666;margin-top:3px;">
            <span style="color:#28a745;">■</span> Resueltas &nbsp;
            <span style="color:#dc3545;">■</span> No Procede
        </div>
    </div>

    <div class="footer">
        Escuela de Jueces del Estado — Sistema de Quejas y Sugerencias de Mejora &nbsp;|&nbsp;
        Reporte estadístico generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
    </div>

</body>
</html>
