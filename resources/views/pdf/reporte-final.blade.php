<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            padding: 30px 40px;
        }

        /* Encabezado institucional */
        .header-outer { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-outer td { border: 1px solid #000; vertical-align: middle; }
        .cell-logo {
            width: 15%; text-align: center; padding: 6px 4px; background-color: #fff;
        }
        .cell-logo .logo-text { font-size: 18px; font-weight: bold; color: #fff; letter-spacing: 2px; }
        .cell-logo .logo-sub { font-size: 6.5px; color: #fff; margin-top: 3px; line-height: 1.3; text-transform: uppercase; }
        .cell-titulo { width: 57%; text-align: center; padding: 6px 8px; }
        .cell-titulo .inst { font-size: 12px; font-weight: bold; color: #c8102e; text-transform: uppercase; letter-spacing: 0.3px; }
        .cell-titulo .doc-name { font-size: 10.5px; font-weight: bold; margin-top: 5px; text-transform: uppercase; }
        .cell-meta { width: 28%; padding: 0; }
        .meta-table { width: 100%; border-collapse: collapse; font-size: 9.5px; }
        .meta-table td { border: 1px solid #000; padding: 4px 6px; }
        .meta-table .meta-label { font-weight: bold; width: 55%; }
        .meta-table .meta-value { text-align: center; }

        /* Secciones */
        .section { margin-bottom: 14px; }
        .section-title {
            font-size: 10px; font-weight: bold; text-transform: uppercase;
            color: #fff; background-color: #c8102e;
            padding: 4px 8px; margin-bottom: 8px; letter-spacing: 0.5px;
        }

        /* Tablas de datos */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td { padding: 5px 8px; border: 1px solid #dee2e6; vertical-align: top; }
        .data-table .label { width: 32%; background-color: #f4f6f9; font-weight: bold; color: #c8102e; }
        .data-table .value { width: 68%; }

        /* Resultado final */
        .resultado-resuelto {
            background-color: #d4edda; border: 1px solid #c3e6cb;
            border-left: 5px solid #28a745; padding: 10px 14px;
            font-size: 12px; font-weight: bold; color: #155724;
        }
        .resultado-no-procede {
            background-color: #f8d7da; border: 1px solid #f5c6cb;
            border-left: 5px solid #dc3545; padding: 10px 14px;
            font-size: 12px; font-weight: bold; color: #721c24;
        }

        /* Timeline de seguimientos */
        .timeline { width: 100%; }
        .timeline-item { margin-bottom: 10px; border-left: 3px solid #c8102e; padding-left: 10px; }
        .timeline-header { font-size: 10px; margin-bottom: 3px; }
        .timeline-date { color: #888; }
        .timeline-actor { font-weight: bold; }
        .badge {
            display: inline-block; padding: 2px 8px; border-radius: 3px;
            font-size: 9.5px; font-weight: bold; color: #fff; margin-right: 5px;
        }
        .badge-warning   { background-color: #ffc107; color: #212529; }
        .badge-info      { background-color: #17a2b8; }
        .badge-primary   { background-color: #007bff; }
        .badge-secondary { background-color: #6c757d; }
        .badge-purple    { background-color: #6f42c1; }
        .badge-success   { background-color: #28a745; }
        .badge-danger    { background-color: #dc3545; }

        .timeline-accion { font-size: 10.5px; margin-bottom: 2px; }
        .timeline-comentario {
            font-size: 10px; color: #555; margin-top: 3px;
            border-left: 2px solid #dee2e6; padding-left: 8px;
            font-style: italic;
        }

        /* Etapas resumen */
        .etapas-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .etapas-table th { background-color: #6c757d; color: #fff; padding: 5px 8px; text-align: center; border: 1px solid #dee2e6; }
        .etapas-table td { padding: 5px 8px; border: 1px solid #dee2e6; text-align: center; }
        .etapas-table .pendiente { color: #888; }

        /* Descripción */
        .descripcion-box {
            border: 1px solid #dee2e6; border-left: 4px solid #c8102e;
            padding: 10px 12px; background-color: #f8f9fa;
            font-size: 11px; line-height: 1.6;
        }

        /* Pie */
        .footer {
            border-top: 1px solid #dee2e6; margin-top: 24px;
            padding-top: 8px; font-size: 9px; color: #888; text-align: center;
        }
    </style>
</head>
<body>

    {{-- Encabezado institucional --}}
    <table class="header-outer">
        <tr>
            <td class="cell-logo">
                <img src="{{ public_path('vendor/adminlte/dist/img/logoeje.png') }}"
                     style="max-width:100%; max-height:70px;">
            </td>
            <td class="cell-titulo">
                <div class="inst">Escuela de Jueces del Estado</div>
                <div class="doc-name">Formulario de Respuesta Quejas o Sugerencias de Mejora</div>
            </td>
            <td class="cell-meta">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Código</td>
                        <td class="meta-value">EJE-E-REG-31</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Versión</td>
                        <td class="meta-value">1</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Vigente desde</td>
                        <td class="meta-value">19/01/2026</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Número y tipo --}}
    <div class="section">
        <div class="section-title">Identificación de la Solicitud</div>
        <table class="data-table">
            <tr>
                <td class="label">N° de Solicitud</td>
                <td class="value">{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="label">Tipo de Solicitud</td>
                <td class="value">{{ \App\Models\Queja::TIPOS[$queja->tipo_solicitud] ?? ucfirst($queja->tipo_solicitud) }}</td>
            </tr>
        </table>
    </div>


    {{-- Datos del solicitante --}}
    <div class="section">
        <div class="section-title">Datos del Usuario</div>
        <table class="data-table">
            <tr>
                <td class="label">Nombre completo</td>
                <td class="value">{{ $queja->user->name }}</td>
            </tr>
            <tr>
                <td class="label">Correo electrónico</td>
                <td class="value">{{ $queja->user->email }}</td>
            </tr>
        </table>
    </div>

    {{-- Datos de la solicitud --}}
    <div class="section">
        <div class="section-title">Detalles del curso de formación o acción de capacitación judicial</div>
        <table class="data-table">
            <tr>
                <td class="label">Nombre de la actividad</td>
                <td class="value">{{ $queja->nombre_actividad }}</td>
            </tr>
        </table>
    </div>


    {{-- Respuesta final --}}
    @php
        $segCierre = $queja->seguimientos
            ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
            ->sortByDesc('created_at')
            ->first();
    @endphp
    <div class="section">
        <div class="section-title">Respuesta a la queja o sugerencia de mejora</div>
        <table class="data-table">
            <tr>
                <td class="label">Respuesta</td>
                <td class="value">
                    @if ($segCierre?->comentario)
                        <div class="descripcion-box">{{ $segCierre->comentario }}</div>
                    @else
                        —
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Pie --}}
    <div class="footer">
        Escuela de Jueces del Estado — Sistema de Quejas y Sugerencias &nbsp;|&nbsp;
        Reporte final generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
    </div>

</body>
</html>
