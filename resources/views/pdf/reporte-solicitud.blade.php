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
        .header-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .header-outer td {
            border: 1px solid #000;
            vertical-align: middle;
        }
        .cell-logo {
            width: 15%;
            text-align: center;
            padding: 6px 4px;
            background-color: #fff;
        }
        .cell-logo .logo-text {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 2px;
        }
        .cell-logo .logo-sub {
            font-size: 6.5px;
            color: #fff;
            margin-top: 3px;
            line-height: 1.3;
            text-transform: uppercase;
        }
        .cell-titulo {
            width: 57%;
            text-align: center;
            padding: 6px 8px;
        }
        .cell-titulo .inst {
            font-size: 12px;
            font-weight: bold;
            color: #c8102e;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .cell-titulo .doc-name {
            font-size: 10.5px;
            font-weight: bold;
            margin-top: 5px;
            text-transform: uppercase;
        }
        .cell-meta {
            width: 28%;
            padding: 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }
        .meta-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        .meta-table .meta-label { font-weight: bold; width: 55%; }
        .meta-table .meta-value { text-align: center; }

        /* Secciones */
        .section {
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            background-color: #c8102e;
            padding: 4px 8px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        /* Filas de datos */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        .data-table .label {
            width: 30%;
            background-color: #f4f6f9;
            font-weight: bold;
            color: #c8102e;
        }
        .data-table .value { width: 70%; }

        /* Descripción */
        .descripcion-box {
            border: 1px solid #dee2e6;
            border-left: 4px solid #c8102e;
            padding: 10px 12px;
            background-color: #f8f9fa;
            font-size: 11px;
            line-height: 1.6;
        }

        /* Estado */
        .estado-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            background-color: {{ $estadoColor }};
        }

        /* Pie */
        .footer {
            border-top: 1px solid #dee2e6;
            margin-top: 30px;
            padding-top: 8px;
            font-size: 9px;
            color: #888;
            text-align: center;
        }

        .note {
            background-color: #fff8e1;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 8px 12px;
            font-size: 10px;
            color: #555;
            margin-top: 10px;
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
                <div class="doc-name">Formulario de Quejas o Sugerencias de Mejora</div>
            </td>
            <td class="cell-meta">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Código</td>
                        <td class="meta-value">EJE-E-REG-30</td>
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

    {{-- Párrafo introductorio --}}
    <p style="font-size:10.5px; margin-bottom:16px; line-height:1.6; text-align:justify;">
        Estimado(a) usuario, si usted no se encuentra satisfecho con nuestros servicios académicos
        o detectó una manera en la que podamos mejorar nuestros servicios, tiene la opción de
        hacernos llegar una queja u sugerencia de mejora, gracias por ayudarnos a mejorar:
    </p>

    {{-- Número de solicitud --}}
    <div class="section">
        <div class="section-title">Número de Solicitud</div>
        <table class="data-table">
            <tr>
                <td class="label">N° de Solicitud</td>
                <td class="value">{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}</td>
            </tr>
        </table>
    </div>

    {{-- Tipo de solicitud --}}
    <div class="section">
        <div class="section-title">Tipo de Solicitud</div>
        <table class="data-table">
            <tr>
                <td class="label">Tipo de Solicitud</td>
                <td class="value">{{ ucfirst($queja->tipo_solicitud) }}</td>
            </tr>
        </table>
    </div>

    {{-- Datos del solicitante --}}
    <div class="section">
        <div class="section-title">Datos del Solicitante</div>
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
        <div class="section-title">Datos de la Solicitud</div>
        <table class="data-table">
            <tr>
                <td class="label">Servicio</td>
                <td class="value">{{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</td>
            </tr>
            <tr>
                <td class="label">Nombre de la actividad</td>
                <td class="value">{{ $queja->nombre_actividad }}</td>
            </tr>
            <tr>
                <td class="label">Respaldo adjunto</td>
                <td class="value">{{ $queja->respaldo ? 'Con respaldo' : 'Sin documento de respaldo' }}</td>
            </tr>
        </table>
    </div>

    {{-- Descripción --}}
    <div class="section">
        <div class="section-title">Descripción de la Queja o Sugerencia de Mejora</div>
        <div class="note" style="margin-bottom:8px;">
            <strong>Nota:</strong> No aplica para reclamos o impugnaciones de calificaciones, el mismo se resuelve conforme solicitud de revisión de notas o impugnaciones descritas en cada Reglamento Específico de Formación o de Capacitación.
        </div>
        <div class="descripcion-box">{{ $queja->descripcion }}</div>
    </div>

    {{-- Nota general --}}
    <div class="note">
        <strong>Nota:</strong> Este documento es una constancia del registro inicial de la solicitud en el Sistema de Quejas y Sugerencias de la Escuela de Jueces del Estado. Los datos consignados corresponden exclusivamente a la información proporcionada por el solicitante al momento del registro.
    </div>

    {{-- Fecha de registro --}}
    <div class="section" style="margin-top:12px;">
        <table class="data-table">
            <tr>
                <td class="label">Fecha de Registro</td>
                <td class="value">{{ $queja->created_at->format('d \d\e F \d\e Y, H:i') }}</td>
            </tr>
        </table>
    </div>

    {{-- Pie --}}
    <div class="footer">
        Escuela de Jueces del Estado — Sistema de Quejas y Sugerencias &nbsp;|&nbsp;
        Documento generado automáticamente el {{ now()->format('d/m/Y \a \l\a\s H:i') }}
    </div>

</body>
</html>
