<?php

namespace App\Livewire\Quejas;

use App\Models\Queja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ControlFlujoQuejas extends Component
{
    use WithPagination;

    public string $filterEstado   = '';
    public string $filterServicio = '';
    public string $filterTipo     = '';
    public string $search         = '';
    public string $fechaDesde     = '';
    public string $fechaHasta     = '';

    public bool $showDetalle = false;
    public ?int $detalleId   = null;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void         { $this->resetPage(); }
    public function updatingFilterEstado(): void   { $this->resetPage(); }
    public function updatingFilterServicio(): void { $this->resetPage(); }
    public function updatingFilterTipo(): void     { $this->resetPage(); }
    public function updatingFechaDesde(): void     { $this->resetPage(); }
    public function updatingFechaHasta(): void     { $this->resetPage(); }

    public function exportPdf(): mixed
    {
        return redirect()->route('quejas.control.flujo.pdf', array_filter([
            'desde'    => $this->fechaDesde,
            'hasta'    => $this->fechaHasta,
            'estado'   => $this->filterEstado,
            'servicio' => $this->filterServicio,
            'tipo'     => $this->filterTipo,
        ]));
    }

    public function verDetalle(int $id): void
    {
        $this->detalleId   = $id;
        $this->showDetalle = true;
    }

    public function cerrarDetalle(): void
    {
        $this->showDetalle = false;
        $this->detalleId   = null;
    }

    private function calcularEtapas(Queja $queja): array
    {
        $segs = $queja->seguimientos->sortBy('created_at')->values();
        $inicio = $queja->created_at;

        $etapas = [];

        // Registro
        $etapas[] = [
            'nombre'   => 'Registro',
            'actor'    => $queja->user->name,
            'fecha'    => $inicio,
            'duracion' => null,
            'color'    => 'secondary',
        ];

        $estados = [
            'en_revision'          => ['nombre' => 'En Revisión',            'color' => 'info'],
            'derivado_coordinador' => ['nombre' => 'Derivado a Coordinador', 'color' => 'primary'],
            'respondido'           => ['nombre' => 'Respondido',             'color' => 'secondary'],
            'en_validacion'        => ['nombre' => 'En Validación',          'color' => 'purple'],
            'resuelto'             => ['nombre' => 'Resuelto',               'color' => 'success'],
            'no_procede'           => ['nombre' => 'No Procede',             'color' => 'danger'],
        ];

        $anterior = $inicio;
        foreach ($segs as $seg) {
            if (!isset($estados[$seg->estado_nuevo])) continue;

            $duracion = $anterior->diffInMinutes($seg->created_at);

            $nombre = $seg->estado_nuevo === 'derivado_coordinador'
                ? $seg->accion
                : $estados[$seg->estado_nuevo]['nombre'];

            $etapas[] = [
                'nombre'   => $nombre,
                'actor'    => $seg->user->name,
                'fecha'    => $seg->created_at,
                'duracion' => $duracion,
                'color'    => $estados[$seg->estado_nuevo]['color'],
            ];

            $anterior = $seg->created_at;
        }

        return $etapas;
    }

    private function duracionTotal(Queja $queja): ?int
    {
        $ultimo = $queja->seguimientos
            ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
            ->sortByDesc('created_at')
            ->first();

        return $ultimo
            ? (int) $queja->created_at->diffInMinutes($ultimo->created_at)
            : null;
    }

    private function formatearDuracion(int $minutos): string
    {
        if ($minutos < 60) {
            return "{$minutos} min";
        }
        if ($minutos < 1440) {
            $h = intdiv($minutos, 60);
            $m = $minutos % 60;
            return $m > 0 ? "{$h}h {$m}min" : "{$h}h";
        }
        $dias  = intdiv($minutos, 1440);
        $horas = intdiv($minutos % 1440, 60);
        return $horas > 0 ? "{$dias}d {$horas}h" : "{$dias}d";
    }

    public function render()
    {
        $user   = Auth::user();
        $search = $this->search;

        $servicioJefe = match($user->role) {
            'jefe_formacion'      => 'formacion',
            'jefe_capacitacion'   => 'capacitacion',
            'jefe_administrativo' => 'administrativo',
            default               => null,
        };

        $quejas = Queja::when($user->isJefeUnidad(), function ($q) use ($user, $servicioJefe) {
                        return $servicioJefe
                            ? $q->where('servicio', $servicioJefe)
                            : $q->where('jefe_id', $user->id);
                    },
                    fn($q) => $q->where('revisor_id', $user->id)
                    )
            ->when($this->filterEstado,   fn($q) => $q->where('estado', $this->filterEstado))
            ->when($this->filterServicio, fn($q) => $q->where('servicio', $this->filterServicio))
            ->when($this->filterTipo,     fn($q) => $q->where('tipo_solicitud', $this->filterTipo))
            ->when($this->fechaDesde,     fn($q) => $q->whereDate('created_at', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,     fn($q) => $q->whereDate('created_at', '<=', $this->fechaHasta))
            ->when($search, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhere('nombre_actividad', 'like', "%{$search}%")
                )
            )
            ->with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])
            ->latest()
            ->paginate(12);

        $quejaDetalle = null;
        $etapas       = [];
        $durTotal     = null;

        if ($this->detalleId) {
            $quejaDetalle = Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])
                ->find($this->detalleId);

            if ($quejaDetalle) {
                $etapas   = $this->calcularEtapas($quejaDetalle);
                $durTotal = $this->duracionTotal($quejaDetalle);
            }
        }

        return view('livewire.quejas.control-flujo-quejas', [
            'quejas'       => $quejas,
            'estados'      => Queja::ESTADOS,
            'servicios'    => Queja::SERVICIOS,
            'tipos'        => Queja::TIPOS,
            'quejaDetalle' => $quejaDetalle,
            'etapas'       => $etapas,
            'durTotal'     => $durTotal,
        ]);
    }
}
