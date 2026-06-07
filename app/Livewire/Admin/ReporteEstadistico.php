<?php

namespace App\Livewire\Admin;

use App\Exports\EstadisticoExport;
use App\Models\Queja;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Url;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReporteEstadistico extends Component
{
    public string $fechaDesde    = '';
    public string $fechaHasta    = '';

    #[Url(as: 'servicio')]
    public string $filterServicio = '';

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

    private function baseQuery()
    {
        $servicioForzado = $this->servicioForzado();

        return Queja::query()
            ->when($servicioForzado,                           fn($q) => $q->where('servicio', $servicioForzado))
            ->when(!$servicioForzado && $this->filterServicio, fn($q) => $q->where('servicio', $this->filterServicio))
            ->when($this->fechaDesde, fn($q) => $q->whereDate('created_at', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('created_at', '<=', $this->fechaHasta));
    }

    private function estadisticas(): array
    {
        $q = $this->baseQuery();

        $total = (clone $q)->count();

        // Por tipo
        $porTipo = [];
        foreach (Queja::TIPOS as $clave => $etiqueta) {
            $porTipo[$etiqueta] = (clone $q)->where('tipo_solicitud', $clave)->count();
        }

        // Por servicio
        $porServicio = [];
        foreach (Queja::SERVICIOS as $clave => $etiqueta) {
            $porServicio[$etiqueta] = (clone $q)->where('servicio', $clave)->count();
        }

        // Por estado
        $porEstado = [];
        foreach (Queja::ESTADOS as $clave => $etiqueta) {
            $porEstado[$etiqueta] = (clone $q)->where('estado', $clave)->count();
        }

        // Por departamento
        $porDepartamento = [];
        foreach (User::DEPARTAMENTOS as $clave => $etiqueta) {
            $cnt = (clone $q)->whereHas('user', fn($u) => $u->where('departamento', $clave))->count();
            if ($cnt > 0) {
                $porDepartamento[$etiqueta] = $cnt;
            }
        }
        $sinDep = (clone $q)->whereHas('user', fn($u) => $u->whereNull('departamento')->orWhere('departamento', ''))->count();
        if ($sinDep > 0) {
            $porDepartamento['Sin departamento'] = $sinDep;
        }

        // Resumen de resolución
        $resueltas  = (clone $q)->where('estado', 'resuelto')->count();
        $noProcede  = (clone $q)->where('estado', 'no_procede')->count();
        $enProceso  = $total - $resueltas - $noProcede;
        $tasaExito  = $total > 0 ? round(($resueltas / $total) * 100, 1) : 0;

        // Tendencia mensual
        $tendencia = (clone $q)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn($r) => [
                'mes'   => \Carbon\Carbon::createFromFormat('Y-m', $r->mes)->format('M Y'),
                'total' => $r->total,
            ]);

        return compact(
            'total', 'porTipo', 'porServicio', 'porEstado',
            'porDepartamento', 'resueltas', 'noProcede', 'enProceso',
            'tasaExito', 'tendencia'
        );
    }

    public function exportExcel()
    {
        $stats   = $this->estadisticas();
        $sf      = $this->servicioForzado() ?? ($this->filterServicio ?: null);
        $filtros = [
            'desde'    => $this->fechaDesde,
            'hasta'    => $this->fechaHasta,
            'servicio' => $sf ? (Queja::SERVICIOS[$sf] ?? $sf) : null,
        ];
        $nombre = 'reporte-estadistico-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new EstadisticoExport($stats, $filtros), $nombre);
    }

    public function exportPdf()
    {
        $stats   = $this->estadisticas();
        $sf      = $this->servicioForzado() ?? ($this->filterServicio ?: null);
        $filtros = [
            'desde'    => $this->fechaDesde,
            'hasta'    => $this->fechaHasta,
            'servicio' => $sf ? (Queja::SERVICIOS[$sf] ?? $sf) : null,
        ];

        $pdf = Pdf::loadView('pdf.reporte-estadistico', compact('stats', 'filtros'))
            ->setPaper('letter', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-estadistico-' . now()->format('Ymd-His') . '.pdf');
    }

    public function render()
    {
        $stats           = $this->estadisticas();
        $servicioForzado = $this->servicioForzado();

        return view('livewire.admin.reporte-estadistico', [
            'stats'           => $stats,
            'servicios'       => Queja::SERVICIOS,
            'servicioForzado' => $servicioForzado,
        ]);
    }
}
