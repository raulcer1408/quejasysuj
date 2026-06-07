<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:DejaVu Sans,sans-serif; font-size:9px; color:#1a1a1a; padding:14px 18px; }

        .header { width:100%; border-collapse:collapse; margin-bottom:10px; }
        .header td { border:1px solid #000; vertical-align:middle; }
        .cell-logo { width:9%; text-align:center; padding:4px; background:#fff; }
        .cell-logo img { max-width:100%; max-height:50px; }
        .cell-titulo { width:67%; text-align:center; padding:4px 8px; }
        .cell-titulo .inst { font-size:12px; font-weight:bold; color:#c8102e; text-transform:uppercase; }
        .cell-titulo .sub  { font-size:9.5px; font-weight:bold; margin-top:2px; }
        .cell-meta { width:24%; padding:0; }
        .meta-table { width:100%; border-collapse:collapse; font-size:8.5px; }
        .meta-table td { border:1px solid #000; padding:3px 5px; }
        .meta-label { font-weight:bold; }

        .filtros { background:#f8f9fa; border:1px solid #dee2e6; border-left:4px solid #c8102e;
                   padding:5px 8px; margin-bottom:8px; font-size:8.5px; }
        .filtros strong { color:#c8102e; }

        table.datos { width:100%; border-collapse:collapse; font-size:8.5px; }
        table.datos th { background:#c8102e; color:#fff; padding:4px 5px; text-align:center;
                         border:1px solid #aaa; }
        table.datos td { padding:3px 5px; border:1px solid #dee2e6; vertical-align:top; }
        table.datos tr:nth-child(even) { background:#f8f9fa; }
        table.datos tr.lenta { background:#fff3cd; }

        .badge { display:inline-block; padding:2px 5px; border-radius:3px;
                 font-size:7.5px; font-weight:bold; color:#fff; }
        .badge-danger    { background:#dc3545; }
        .badge-info      { background:#17a2b8; }
        .badge-success   { background:#28a745; }
        .badge-warning   { background:#ffc107; color:#212529; }
        .badge-primary   { background:#007bff; }
        .badge-secondary { background:#6c757d; }
        .badge-purple    { background:#6f42c1; }

        .etapas-list { margin:0; padding:0; list-style:none; }
        .etapas-list li { margin-bottom:1px; font-size:7.5px; }

        .footer { border-top:1px solid #dee2e6; margin-top:12px; padding-top:4px;
                  font-size:8px; color:#888; text-align:center; }
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
                <div class="sub">Historial de Control de Flujo — Quejas y Sugerencias de Mejora</div>
            </td>
            <td class="cell-meta">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Unidad</td>
                        <td>{{ $filtros['unidad'] }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Período</td>
                        <td>
                            {{ $filtros['desde'] ? \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') : '—' }}
                            al
                            {{ $filtros['hasta'] ? \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') : '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">Total</td>
                        <td>{{ $quejas->count() }} solicitudes</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Generado</td>
                        <td>{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="filtros">
        <strong>Generado por:</strong> {{ $filtros['usuario'] }}
        &nbsp;|&nbsp;
        <strong>Período:</strong>
        {{ $filtros['desde'] ? \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') : 'Inicio' }}
        al
        {{ $filtros['hasta'] ? \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') : 'hoy' }}
        &nbsp;|&nbsp;
        <strong>Unidad:</strong> {{ $filtros['unidad'] }}
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:6%">Tipo</th>
                <th style="width:8%">Servicio</th>
                <th style="width:18%">Actividad</th>
                <th style="width:12%">Solicitante</th>
                <th style="width:9%">Revisor</th>
                <th style="width:9%">Estado</th>
                <th style="width:7%">Registro</th>
                <th style="width:7%">Cierre</th>
                <th style="width:6%">Días</th>
                <th style="width:13%">Etapas del flujo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($quejas as $queja)
                @php
                    $segCierre = $queja->seguimientos
                        ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                        ->sortByDesc('created_at')->first();
                    $dias = $segCierre
                        ? (int) $queja->created_at->diffInDays($segCierre->created_at)
                        : null;
                    $lenta = $dias !== null && $dias >= 5;

                    $colores = [
                        'pendiente'            => 'warning',
                        'en_revision'          => 'info',
                        'derivado_coordinador' => 'primary',
                        'respondido'           => 'secondary',
                        'en_validacion'        => 'purple',
                        'resuelto'             => 'success',
                        'no_procede'           => 'danger',
                    ];

                    $estadosLabel = [
                        'pendiente'            => 'Pendiente',
                        'en_revision'          => 'En Revisión',
                        'derivado_coordinador' => 'Derivado',
                        'respondido'           => 'Respondido',
                        'en_validacion'        => 'En Validación',
                        'resuelto'             => 'Resuelto',
                        'no_procede'           => 'No Procede',
                    ];

                    $etapasSegs = $queja->seguimientos->sortBy('created_at')->values();
                @endphp
                <tr class="{{ $lenta ? 'lenta' : '' }}">
                    <td style="text-align:center;font-weight:bold;">
                        {{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $queja->tipo_solicitud === 'queja' ? 'danger' : 'info' }}">
                            {{ $queja->tipo_solicitud === 'queja' ? 'Queja' : 'Sugerencia' }}
                        </span>
                    </td>
                    <td>{{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</td>
                    <td>{{ Str::limit($queja->nombre_actividad, 35) }}</td>
                    <td>{{ $queja->user->name }}</td>
                    <td>{{ $queja->revisor?->name ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $colores[$queja->estado] ?? 'secondary' }}">
                            {{ $estadosLabel[$queja->estado] ?? $queja->estado }}
                        </span>
                    </td>
                    <td style="text-align:center;">{{ $queja->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        {{ $segCierre ? $segCierre->created_at->format('d/m/Y') : '—' }}
                    </td>
                    <td style="text-align:center;">
                        @if ($dias !== null)
                            <span style="font-weight:bold;color:{{ $lenta ? '#dc3545' : '#28a745' }};">
                                {{ $dias }}d{{ $lenta ? ' ⚠' : '' }}
                            </span>
                        @else
                            <span style="color:#999;">En curso</span>
                        @endif
                    </td>
                    <td>
                        <ul class="etapas-list">
                            @foreach ($etapasSegs as $seg)
                                @if (isset($estadosLabel[$seg->estado_nuevo]))
                                <li>
                                    <span class="badge badge-{{ $colores[$seg->estado_nuevo] ?? 'secondary' }}">
                                        {{ $estadosLabel[$seg->estado_nuevo] }}
                                    </span>
                                    {{ $seg->created_at->format('d/m') }}
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:12px;color:#888;">
                        No hay solicitudes para el período y filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Escuela de Jueces del Estado — Sistema de Quejas y Sugerencias de Mejora
        &nbsp;|&nbsp; Reporte de Control de Flujo generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
        &nbsp;|&nbsp; ⚠ Solicitudes con 5 o más días de atención
    </div>

</body>
</html>
