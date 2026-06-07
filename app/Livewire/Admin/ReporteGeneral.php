<?php

namespace App\Livewire\Admin;

use App\Exports\SolicitudesExport;
use App\Models\Queja;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReporteGeneral extends Component
{
    use WithPagination;

    public string $filterEstado      = '';
    public string $filterServicio    = '';
    public string $filterTipo        = '';
    public string $filterDepartamento = '';
    public string $fechaDesde        = '';
    public string $fechaHasta        = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingFilterEstado(): void      { $this->resetPage(); }
    public function updatingFilterServicio(): void    { $this->resetPage(); }
    public function updatingFilterTipo(): void        { $this->resetPage(); }
    public function updatingFilterDepartamento(): void { $this->resetPage(); }
    public function updatingFechaDesde(): void        { $this->resetPage(); }
    public function updatingFechaHasta(): void        { $this->resetPage(); }

    private function filters(): array
    {
        return [
            'estado'       => $this->filterEstado       ?: null,
            'servicio'     => $this->filterServicio     ?: null,
            'tipo'         => $this->filterTipo         ?: null,
            'departamento' => $this->filterDepartamento ?: null,
            'desde'        => $this->fechaDesde         ?: null,
            'hasta'        => $this->fechaHasta         ?: null,
        ];
    }

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

        return Queja::with(['user', 'revisor', 'jefe', 'seguimientos'])
            ->when($servicioForzado,          fn($q) => $q->where('servicio', $servicioForzado))
            ->when(!$servicioForzado && $this->filterServicio,
                                              fn($q) => $q->where('servicio', $this->filterServicio))
            ->when($this->filterEstado,       fn($q) => $q->where('estado', $this->filterEstado))
            ->when($this->filterTipo,         fn($q) => $q->where('tipo_solicitud', $this->filterTipo))
            ->when($this->filterDepartamento, fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('departamento', $this->filterDepartamento))
            )
            ->when($this->fechaDesde, fn($q) => $q->whereDate('created_at', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('created_at', '<=', $this->fechaHasta))
            ->latest();
    }

    public function exportExcel(): BinaryFileResponse
    {
        $nombre = 'reporte-solicitudes-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new SolicitudesExport($this->filters()), $nombre);
    }

    public function exportPdf()
    {
        $quejas = $this->baseQuery()->get();
        $filtros = [
            'estado'       => $this->filterEstado,
            'servicio'     => $this->filterServicio,
            'tipo'         => $this->filterTipo,
            'departamento' => $this->filterDepartamento,
            'desde'        => $this->fechaDesde,
            'hasta'        => $this->fechaHasta,
        ];

        $pdf = Pdf::loadView('pdf.reporte-general', compact('quejas', 'filtros'))
            ->setPaper('letter', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-solicitudes-' . now()->format('Ymd-His') . '.pdf');
    }

    public function render()
    {
        $quejas = $this->baseQuery()->paginate(15);
        $total  = $this->baseQuery()->count();

        return view('livewire.admin.reporte-general', [
            'quejas'          => $quejas,
            'total'           => $total,
            'estados'         => Queja::ESTADOS,
            'servicios'       => Queja::SERVICIOS,
            'tipos'           => Queja::TIPOS,
            'departamentos'   => User::DEPARTAMENTOS,
            'servicioForzado' => $this->servicioForzado(),
        ]);
    }
}
