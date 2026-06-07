<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ResumenSheet implements WithTitle, WithEvents, ShouldAutoSize
{
    private const RED   = 'FFC8102E';
    private const WHITE = 'FFFFFFFF';
    private const GRAY  = 'FFF4F6F9';
    private const DARK  = 'FF1A1A1A';

    public function __construct(
        private readonly array $stats,
        private readonly array $filtros
    ) {}

    public function title(): string { return 'Resumen Estadístico'; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row   = 1;

                // ── Encabezado institucional ──────────────────────────────────
                $sheet->mergeCells("B{$row}:G{$row}");
                $sheet->setCellValue("B{$row}", 'Escuela de Jueces del Estado');
                $this->style($sheet, "B{$row}:G{$row}", [
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => self::RED]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(50);

                $logoPath = public_path('vendor/adminlte/dist/img/logoeje.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo EJE')->setPath($logoPath)
                            ->setHeight(48)->setCoordinates("A{$row}")
                            ->setOffsetX(3)->setOffsetY(2)
                            ->setWorksheet($sheet);
                }

                $row++;
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->setCellValue("A{$row}", 'Reporte Estadístico — Quejas y Sugerencias de Mejora');
                $this->style($sheet, "A{$row}:G{$row}", [
                    'font'      => ['bold' => true, 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GRAY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
                $periodo = ($this->filtros['desde'] || $this->filtros['hasta'])
                    ? (($this->filtros['desde'] ?? '—') . ' al ' . ($this->filtros['hasta'] ?? '—'))
                    : 'Todo el período';
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->setCellValue("A{$row}", "Período: {$periodo}   |   Generado: " . now()->format('d/m/Y H:i'));
                $this->style($sheet, "A{$row}:G{$row}", [
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FF666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(14);

                $row += 2;

                // ── Totalizador general ───────────────────────────────────────
                $row = $this->bloque($sheet, $row, 'RESUMEN GENERAL', [
                    ['Total solicitudes',         $this->stats['total']],
                    ['Resueltas',                 $this->stats['resueltas']],
                    ['No procede',                $this->stats['noProcede']],
                    ['En proceso',                $this->stats['enProceso']],
                    ['Tasa de resolución (%)',    $this->stats['tasaExito'] . '%'],
                ]);

                $row++;

                // ── Por tipo ──────────────────────────────────────────────────
                $row = $this->bloque($sheet, $row, 'POR TIPO DE SOLICITUD',
                    array_map(fn($k, $v) => [$k, $v],
                        array_keys($this->stats['porTipo']),
                        array_values($this->stats['porTipo'])
                    )
                );

                $row++;

                // ── Por servicio ──────────────────────────────────────────────
                $row = $this->bloque($sheet, $row, 'POR SERVICIO',
                    array_map(fn($k, $v) => [$k, $v],
                        array_keys($this->stats['porServicio']),
                        array_values($this->stats['porServicio'])
                    )
                );

                $row++;

                // ── Por estado ────────────────────────────────────────────────
                $row = $this->bloque($sheet, $row, 'POR ESTADO',
                    array_map(fn($k, $v) => [$k, $v],
                        array_keys($this->stats['porEstado']),
                        array_values($this->stats['porEstado'])
                    )
                );

                $row++;

                // ── Por departamento ──────────────────────────────────────────
                if (!empty($this->stats['porDepartamento'])) {
                    $row = $this->bloque($sheet, $row, 'POR DEPARTAMENTO DE ORIGEN',
                        array_map(fn($k, $v) => [$k, $v],
                            array_keys($this->stats['porDepartamento']),
                            array_values($this->stats['porDepartamento'])
                        )
                    );
                    $row++;
                }

                // ── Tendencia mensual ─────────────────────────────────────────
                if ($this->stats['tendencia']->isNotEmpty()) {
                    $row = $this->bloque($sheet, $row, 'TENDENCIA MENSUAL',
                        $this->stats['tendencia']->map(fn($t) => [$t['mes'], $t['total']])->toArray()
                    );
                }
            },
        ];
    }

    private function bloque($sheet, int $row, string $titulo, array $filas): int
    {
        // Título del bloque
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", $titulo);
        $this->style($sheet, "A{$row}:B{$row}", [
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => self::WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RED]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        // Encabezados internos
        $sheet->setCellValue("A{$row}", 'Categoría');
        $sheet->setCellValue("B{$row}", 'Total');
        $this->style($sheet, "A{$row}:B{$row}", [
            'font' => ['bold' => true, 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GRAY]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        // Filas de datos
        foreach ($filas as $i => $fila) {
            $sheet->setCellValue("A{$row}", $fila[0]);
            $sheet->setCellValue("B{$row}", $fila[1]);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            if ($i % 2 === 0) {
                $this->style($sheet, "A{$row}:B{$row}", [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFAFAFA']],
                ]);
            }
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR]],
            ]);
            $row++;
        }

        return $row;
    }

    private function style($sheet, string $range, array $styles): void
    {
        $sheet->getStyle($range)->applyFromArray($styles);
    }
}
