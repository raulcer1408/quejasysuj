<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Queja;

class AtencionIndividualExport implements WithTitle, WithEvents, ShouldAutoSize
{
    private const RED   = 'FFC8102E';
    private const WHITE = 'FFFFFFFF';
    private const GRAY  = 'FFF4F6F9';

    public function __construct(
        private readonly array $intervenciones,
        private readonly array $resumen,
        private readonly array $filtros
    ) {}

    public function title(): string { return 'Atención Individual'; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row   = 1;

                // Encabezado
                $sheet->mergeCells("B{$row}:I{$row}");
                $sheet->setCellValue("B{$row}", 'Escuela de Jueces del Estado');
                $sheet->getStyle("B{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => self::RED]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(50);

                $logoPath = public_path('vendor/adminlte/dist/img/logoeje.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo')->setPath($logoPath)
                            ->setHeight(48)->setCoordinates("A{$row}")
                            ->setOffsetX(3)->setOffsetY(2)->setWorksheet($sheet);
                }

                $row++;
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->setCellValue("A{$row}", 'Reporte de Atención Individual por Actor');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GRAY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(18);

                $row++;
                $periodo = ($this->filtros['desde'] || $this->filtros['hasta'])
                    ? (($this->filtros['desde'] ?? '—') . ' al ' . ($this->filtros['hasta'] ?? '—'))
                    : 'Todo el período';
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->setCellValue("A{$row}", "Período: {$periodo}   |   Generado: " . now()->format('d/m/Y H:i'));
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FF666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(14);

                $row += 2;

                // ── Resumen por usuario ───────────────────────────────────────
                $this->titulo($sheet, $row, 'RESUMEN POR ACTOR', 'I');
                $row++;
                foreach (['Actor', 'Rol(es)', 'Intervenciones', 'Promedio (h)', 'Mínimo (h)', 'Máximo (h)', 'Promedio (días)'] as $col => $cab) {
                    $sheet->setCellValue(chr(65 + $col) . $row, $cab);
                }
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GRAY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                foreach ($this->resumen as $i => $r) {
                    $vals = [
                        $r['user']->name,
                        implode(', ', $r['roles']),
                        $r['intervenciones'],
                        $r['promedio_horas'],
                        $r['horas_min'],
                        $r['horas_max'],
                        $r['promedio_dias'],
                    ];
                    foreach ($vals as $col => $v) {
                        $sheet->setCellValue(chr(65 + $col) . $row, $v);
                    }
                    $sheet->getStyle("C{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFAFAFA']],
                        ]);
                    }
                    $row++;
                }

                $row++;

                // ── Detalle de intervenciones ─────────────────────────────────
                $this->titulo($sheet, $row, 'DETALLE DE INTERVENCIONES', 'I');
                $row++;
                $cabeceras = ['Actor', 'Rol', 'N° Solicitud', 'Servicio', 'Tipo',
                              'Recibido en', 'Respondió en', 'Horas', 'Días'];
                foreach ($cabeceras as $col => $cab) {
                    $sheet->setCellValue(chr(65 + $col) . $row, $cab);
                }
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RED]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;
                foreach ($this->intervenciones as $i => $item) {
                    $q    = $item['queja'];
                    $vals = [
                        $item['user']->name,
                        $item['rol'],
                        str_pad($q->id, 5, '0', STR_PAD_LEFT),
                        Queja::SERVICIOS[$q->servicio]    ?? $q->servicio,
                        Queja::TIPOS[$q->tipo_solicitud]  ?? $q->tipo_solicitud,
                        $item['recibido_en']->format('d/m/Y H:i'),
                        $item['respondio_en']->format('d/m/Y H:i'),
                        $item['horas'],
                        $item['dias'],
                    ];
                    foreach ($vals as $col => $v) {
                        $sheet->setCellValue(chr(65 + $col) . $row, $v);
                    }
                    $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFAFAFA']],
                        ]);
                    }
                    $row++;
                }
            },
        ];
    }

    private function titulo($sheet, int $row, string $txt, string $lastCol): void
    {
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", $txt);
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RED]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(16);
    }
}
