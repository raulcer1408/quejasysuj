<?php

namespace App\Livewire\Admin;

use App\Exports\TiemposExport;
use App\Models\Queja;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ReporteTiempos extends Component
{
    use WithPagination;

    public string $fechaDesde    = '';
    public string $fechaHasta    = '';
    public string $filterServicio = '';
    public int    $umbralDias    = 5;

    protected $paginationTheme = 'bootstrap';

    public function updatingFechaDesde(): void     { $this->resetPage(); }
    public function updatingFechaHasta(): void     { $this->resetPage(); }
    public function updatingFilterServicio(): void { $this->resetPage(); }
    public function updatingUmbralDias(): void     { $this->resetPage(); }

    private function servicioForzado(): ?string
    {
        $user = auth()->user();
        return match($user->role) {
            'jefe_formacion'      => 'formacion',
            'jefe_capacitacion'   => 'capacitacion',
            'jefe_administrativo' => 'administrativo',
            default               => null,
        };
    }

    private function quejasCerradas()
    {
        $servicioForzado = $this->servicioForzado();

        return Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])
            ->whereIn('estado', ['resuelto', 'no_procede'])
            ->when($servicioForzado,               fn($q) => $q->where('servicio', $servicioForzado))
            ->when(!$servicioForzado && $this->filterServicio, fn($q) => $q->where('servicio', $this->filterServicio))
            ->when($this->fechaDesde,              fn($q) => $q->whereDate('created_at', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,              fn($q) => $q->whereDate('created_at', '<=', $this->fechaHasta))
            ->latest();
    }

    private function tiemposQueja(Queja $queja): array
    {
        $segs = $queja->seguimientos->sortBy('created_at');

        $fecha = fn(string $estado) => $segs->firstWhere('estado_nuevo', $estado)?->created_at;
        $fechaCierre = $segs->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                            ->sortByDesc('created_at')->first()?->created_at;

        $inicio      = $queja->created_at;
        $revision    = $fecha('en_revision');
        $derivado    = $fecha('derivado_coordinador');
        $respondido  = $fecha('respondido');
        $validacion  = $fecha('en_validacion');

        $diff = fn($a, $b) => ($a && $b) ? round($a->diffInHours($b) / 24, 1) : null;

        return [
            'total'         => $fechaCierre ? round($inicio->diffInHours($fechaCierre) / 24, 1) : null,
            'hasta_revision'=> $diff($inicio, $revision),
            'en_revision'   => $derivado ? $diff($revision, $derivado)
                                         : ($validacion ? $diff($revision, $validacion) : null),
            'coordinador'   => $diff($derivado, $respondido),
            'hasta_jefe'    => $respondido ? $diff($respondido, $validacion)
                                           : ($derivado ? null : $diff($revision, $validacion)),
            'jefe'          => $validacion ? $diff($validacion, $fechaCierre) : null,
            'cierre'        => $fechaCierre,
        ];
    }

    public function datos(): array
    {
        $quejas  = $this->quejasCerradas()->get();
        $filas   = [];
        $totales = [];

        foreach ($quejas as $queja) {
            $t = $this->tiemposQueja($queja);
            $t['queja'] = $queja;
            $filas[]    = $t;
            if ($t['total'] !== null) $totales[] = $t['total'];
        }

        // Promedios por servicio
        $porServicio = [];
        foreach (Queja::SERVICIOS as $clave => $etiqueta) {
            $ts = collect($filas)
                ->filter(fn($f) => $f['queja']->servicio === $clave && $f['total'] !== null)
                ->pluck('total');
            if ($ts->isNotEmpty()) {
                $porServicio[$etiqueta] = round($ts->avg(), 1);
            }
        }

        // Promedios por revisor
        $porRevisor = [];
        foreach (collect($filas)->groupBy(fn($f) => $f['queja']->revisor?->name ?? 'Sin asignar') as $nombre => $grupo) {
            $ts = $grupo->filter(fn($f) => $f['total'] !== null)->pluck('total');
            if ($ts->isNotEmpty()) {
                $porRevisor[$nombre] = round($ts->avg(), 1);
            }
        }
        arsort($porRevisor);

        return [
            'filas'          => $filas,
            'total'          => count($filas),
            'promedio'       => count($totales) ? round(array_sum($totales) / count($totales), 1) : 0,
            'maximo'         => count($totales) ? max($totales) : 0,
            'minimo'         => count($totales) ? min($totales) : 0,
            'lentas'         => collect($filas)->filter(fn($f) => $f['total'] !== null && $f['total'] > $this->umbralDias)->count(),
            'porServicio'    => $porServicio,
            'porRevisor'     => $porRevisor,
        ];
    }

    public function exportExcel()
    {
        $datos   = $this->datos();
        $filtros = ['desde' => $this->fechaDesde, 'hasta' => $this->fechaHasta,
                    'servicio' => $this->filterServicio, 'umbral' => $this->umbralDias];
        return Excel::download(new TiemposExport($datos, $filtros),
            'reporte-tiempos-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $datos   = $this->datos();
        $filtros = ['desde' => $this->fechaDesde, 'hasta' => $this->fechaHasta,
                    'servicio' => $this->filterServicio, 'umbral' => $this->umbralDias];

        $pdf = Pdf::loadView('pdf.reporte-tiempos', compact('datos', 'filtros'))
            ->setPaper('letter', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-tiempos-' . now()->format('Ymd-His') . '.pdf');
    }

    public function render()
    {
        $datos    = $this->datos();
        $filasPag = collect($datos['filas'])->forPage($this->getPage(), 15);

        return view('livewire.admin.reporte-tiempos', [
            'datos'           => $datos,
            'filasPag'        => $filasPag,
            'servicios'       => Queja::SERVICIOS,
            'umbralDias'      => $this->umbralDias,
            'servicioForzado' => $this->servicioForzado(),
        ]);
    }
}
