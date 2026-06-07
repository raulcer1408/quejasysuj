<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Queja;

class TiemposExport implements WithTitle, WithEvents, ShouldAutoSize
{
    private const RED   = 'FFC8102E';
    private const WHITE = 'FFFFFFFF';
    private const GRAY  = 'FFF4F6F9';

    public function __construct(
        private readonly array $datos,
        private readonly array $filtros
    ) {}

    public function title(): string { return 'Tiempos de Atención'; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row   = 1;

                // ── Encabezado ────────────────────────────────────────────────
                $sheet->mergeCells("B{$row}:J{$row}");
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
                    $drawing->setName('Logo EJE')->setPath($logoPath)
                            ->setHeight(48)->setCoordinates("A{$row}")
                            ->setOffsetX(3)->setOffsetY(2)->setWorksheet($sheet);
                }

                $row++;
                $sheet->mergeCells("A{$row}:J{$row}");
                $sheet->setCellValue("A{$row}", 'Reporte de Tiempos de Atención — Quejas y Sugerencias de Mejora');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GRAY]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
                $periodo = ($this->filtros['desde'] || $this->filtros['hasta'])
                    ? (($this->filtros['desde'] ?? '—') . ' al ' . ($this->filtros['hasta'] ?? '—'))
                    : 'Todo el período';
                $sheet->mergeCells("A{$row}:J{$row}");
                $sheet->setCellValue("A{$row}", "Período: {$periodo}   |   Umbral: {$this->filtros['umbral']} días   |   Generado: " . now()->format('d/m/Y H:i'));
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FF666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(14);

                $row += 2;

                // ── Resumen ───────────────────────────────────────────────────
                $this->bloqueTitulo($sheet, $row, 'RESUMEN GENERAL', 'J');
                $row++;
                $resumen = [
                    ['Total solicitudes cerradas', $this->datos['total']],
                    ['Promedio de atención (días)', $this->datos['promedio']],
                    ['Mínimo (días)',               $this->datos['minimo']],
                    ['Máximo (días)',               $this->datos['maximo']],
                    ['Solicitudes lentas (> ' . $this->filtros['umbral'] . 'd)', $this->datos['lentas']],
                ];
                foreach ($resumen as $i => $fila) {
                    $sheet->setCellValue("A{$row}", $fila[0]);
                    $sheet->setCellValue("B{$row}", $fila[1]);
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFAFAFA']],
                        ]);
                    }
                    $row++;
                }

                $row++;

                // ── Por servicio ──────────────────────────────────────────────
                $this->bloqueTitulo($sheet, $row, 'PROMEDIO POR SERVICIO (días)', 'B');
                $row++;
                $sheet->setCellValue("A{$row}", 'Servicio');
                $sheet->setCellValue("B{$row}", 'Promedio (días)');
                $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GRAY]],
                ]);
                $row++;
                foreach ($this->datos['porServicio'] as $servicio => $prom) {
                    $sheet->setCellValue("A{$row}", $servicio);
                    $sheet->setCellValue("B{$row}", $prom);
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $row++;
                }

                $row++;

                // ── Por revisor ───────────────────────────────────────────────
                $this->bloqueTitulo($sheet, $row, 'PROMEDIO POR REVISOR (días)', 'B');
                $row++;
                $sheet->setCellValue("A{$row}", 'Revisor');
                $sheet->setCellValue("B{$row}", 'Promedio (días)');
                $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::GRAY]],
                ]);
                $row++;
                foreach ($this->datos['porRevisor'] as $revisor => $prom) {
                    $sheet->setCellValue("A{$row}", $revisor);
                    $sheet->setCellValue("B{$row}", $prom);
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $row++;
                }

                $row++;

                // ── Detalle por solicitud ─────────────────────────────────────
                $this->bloqueTitulo($sheet, $row, 'DETALLE POR SOLICITUD', 'J');
                $row++;

                $cabeceras = ['N°','Tipo','Servicio','Solicitante','Revisor','Estado','Registro','Cierre','Total (días)','¿Lenta?'];
                foreach ($cabeceras as $col => $cab) {
                    $letra = chr(65 + $col);
                    $sheet->setCellValue("{$letra}{$row}", $cab);
                }
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RED]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                foreach ($this->datos['filas'] as $i => $f) {
                    $q     = $f['queja'];
                    $lenta = $f['total'] !== null && $f['total'] > $this->filtros['umbral'];
                    $vals  = [
                        str_pad($q->id, 5, '0', STR_PAD_LEFT),
                        Queja::TIPOS[$q->tipo_solicitud] ?? $q->tipo_solicitud,
                        Queja::SERVICIOS[$q->servicio]   ?? $q->servicio,
                        $q->user->name,
                        $q->revisor?->name ?? '—',
                        Queja::ESTADOS[$q->estado] ?? $q->estado,
                        $q->created_at->format('d/m/Y'),
                        $f['cierre']?->format('d/m/Y') ?? '—',
                        $f['total'] ?? '—',
                        $lenta ? 'SÍ' : 'No',
                    ];
                    foreach ($vals as $col => $val) {
                        $sheet->setCellValue(chr(65 + $col) . $row, $val);
                    }
                    if ($lenta) {
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF3CD']],
                        ]);
                    } elseif ($i % 2 === 0) {
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFAFAFA']],
                        ]);
                    }
                    $row++;
                }
            },
        ];
    }

    private function bloqueTitulo($sheet, int $row, string $titulo, string $lastCol): void
    {
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", $titulo);
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => self::WHITE]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RED]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(16);
    }
}
