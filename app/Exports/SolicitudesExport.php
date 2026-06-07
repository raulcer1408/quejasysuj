<?php

namespace App\Exports;

use App\Models\Queja;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SolicitudesExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithCustomStartCell,
    WithEvents
{
    // Número de columnas del reporte
    private const LAST_COL = 'N';
    private const HEADER_ROWS = 4; // filas reservadas para el encabezado institucional

    public function __construct(
        private readonly array $filters
    ) {}

    public function startCell(): string
    {
        // Los datos empiezan después de las filas de encabezado
        return 'A' . (self::HEADER_ROWS + 1);
    }

    public function query()
    {
        return Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos'])
            ->when($this->filters['estado'] ?? null,       fn($q) => $q->where('estado', $this->filters['estado']))
            ->when($this->filters['servicio'] ?? null,     fn($q) => $q->where('servicio', $this->filters['servicio']))
            ->when($this->filters['tipo'] ?? null,         fn($q) => $q->where('tipo_solicitud', $this->filters['tipo']))
            ->when($this->filters['departamento'] ?? null, fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('departamento', $this->filters['departamento']))
            )
            ->when($this->filters['desde'] ?? null, fn($q) => $q->whereDate('created_at', '>=', $this->filters['desde']))
            ->when($this->filters['hasta'] ?? null, fn($q) => $q->whereDate('created_at', '<=', $this->filters['hasta']))
            ->latest();
    }

    public function headings(): array
    {
        return [
            'N° Solicitud',
            'Tipo',
            'Servicio',
            'Nombre Actividad',
            'Solicitante',
            'Correo',
            'Departamento',
            'Estado',
            'Revisor Asignado',
            'Coordinador',
            'Jefe de Unidad',
            'Fecha Registro',
            'Fecha Cierre',
            'Días en Proceso',
        ];
    }

    public function map($queja): array
    {
        $segCierre = $queja->seguimientos
            ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
            ->sortByDesc('created_at')->first();

        $fechaCierre = $segCierre?->created_at;
        $diasProceso = $fechaCierre
            ? (int) $queja->created_at->diffInDays($fechaCierre)
            : '—';

        return [
            str_pad($queja->id, 5, '0', STR_PAD_LEFT),
            Queja::TIPOS[$queja->tipo_solicitud]  ?? $queja->tipo_solicitud,
            Queja::SERVICIOS[$queja->servicio]    ?? $queja->servicio,
            $queja->nombre_actividad,
            $queja->user->name,
            $queja->user->email,
            User::DEPARTAMENTOS[$queja->user->departamento ?? ''] ?? '—',
            Queja::ESTADOS[$queja->estado]        ?? $queja->estado,
            $queja->revisor?->name     ?? '—',
            $queja->coordinador?->name ?? '—',
            $queja->jefe?->name        ?? '—',
            $queja->created_at->format('d/m/Y H:i'),
            $fechaCierre?->format('d/m/Y H:i') ?? '—',
            $diasProceso,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $headingRow = self::HEADER_ROWS + 1;

        return [
            // Fila de encabezados de columna
            $headingRow => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC8102E']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical'   => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                  'color'       => ['argb' => 'FFFFFFFF']]],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Fila 1: logo + título institucional ──────────────────────

                // Celda A1 reservada para el logo (se inserta como imagen)
                // Celdas B1:N1 → nombre institución
                $sheet->mergeCells('B1:' . self::LAST_COL . '1');
                $sheet->setCellValue('B1', 'Escuela de Jueces del Estado');
                $sheet->getStyle('B1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFC8102E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(50);

                // ── Fila 2: subtítulo ─────────────────────────────────────────
                $sheet->mergeCells('A2:' . self::LAST_COL . '2');
                $sheet->setCellValue('A2', 'Reporte General de Quejas y Sugerencias de Mejora');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF1A1A1A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF4F6F9']],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ── Fila 3: fecha de generación ───────────────────────────────
                $sheet->mergeCells('A3:' . self::LAST_COL . '3');
                $sheet->setCellValue('A3', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FF666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(16);

                // ── Fila 4: espacio separador ─────────────────────────────────
                $sheet->getRowDimension(4)->setRowHeight(6);

                // ── Logo ──────────────────────────────────────────────────────
                $logoPath = public_path('vendor/adminlte/dist/img/logoeje.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo EJE');
                    $drawing->setDescription('Escuela de Jueces del Estado');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(50);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(4);
                    $drawing->setOffsetY(2);
                    $drawing->setWorksheet($sheet);
                }

                // ── Bordes en bloque de encabezado institucional ──────────────
                $sheet->getStyle('A1:' . self::LAST_COL . '3')->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_MEDIUM,
                                      'color'       => ['argb' => 'FFC8102E']],
                    ],
                ]);

                // ── Altura mínima de filas de datos ───────────────────────────
                $highestRow = $sheet->getHighestRow();
                for ($i = self::HEADER_ROWS + 2; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(14);
                }

                // ── Filas alternas en datos ───────────────────────────────────
                for ($i = self::HEADER_ROWS + 2; $i <= $highestRow; $i++) {
                    if ($i % 2 === 0) {
                        $sheet->getStyle('A' . $i . ':' . self::LAST_COL . $i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID,
                                       'startColor' => ['argb' => 'FFF8F9FA']],
                        ]);
                    }
                }

                // ── Alineación centrada en columna N° solicitud ───────────────
                $sheet->getStyle('A' . (self::HEADER_ROWS + 1) . ':A' . $highestRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function title(): string
    {
        return 'Solicitudes';
    }
}
