<?php

namespace App\Livewire\Quejas;

use App\Models\Queja;
use Livewire\Component;
use Livewire\WithPagination;

class HistoricoFlujo extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filterEstado   = '';
    public string $filterServicio = '';
    public string $filterTipo     = '';

    public bool $showDetalle = false;
    public ?int $detalleId   = null;

    public bool $showFlujo = false;
    public ?int $flujoId   = null;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void         { $this->resetPage(); }
    public function updatingFilterEstado(): void   { $this->resetPage(); }
    public function updatingFilterServicio(): void { $this->resetPage(); }
    public function updatingFilterTipo(): void     { $this->resetPage(); }

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

    public function verFlujo(int $id): void
    {
        $this->flujoId   = $id;
        $this->showFlujo = true;
    }

    public function cerrarFlujo(): void
    {
        $this->showFlujo = false;
        $this->flujoId   = null;
    }

    public function calcularEtapas(Queja $queja): array
    {
        $segs   = $queja->seguimientos->sortBy('created_at')->values();
        $inicio = $queja->created_at;

        $etapas = [[
            'nombre'   => 'Registro',
            'actor'    => $queja->user->name,
            'fecha'    => $inicio,
            'color'    => 'secondary',
        ]];

        $mapa = [
            'en_revision'          => ['nombre' => 'En Revisión',   'color' => 'info'],
            'derivado_coordinador' => ['nombre' => '',               'color' => 'primary'],
            'respondido'           => ['nombre' => 'Respondido',     'color' => 'secondary'],
            'en_validacion'        => ['nombre' => 'En Validación',  'color' => 'purple'],
            'resuelto'             => ['nombre' => 'Resuelto',       'color' => 'success'],
            'no_procede'           => ['nombre' => 'No Procede',     'color' => 'danger'],
        ];

        foreach ($segs as $seg) {
            if (!isset($mapa[$seg->estado_nuevo])) continue;
            $nombre = $seg->estado_nuevo === 'derivado_coordinador'
                ? $seg->accion
                : $mapa[$seg->estado_nuevo]['nombre'];

            $etapas[] = [
                'nombre' => $nombre,
                'actor'  => $seg->user->name,
                'fecha'  => $seg->created_at,
                'color'  => $mapa[$seg->estado_nuevo]['color'],
            ];
        }

        return $etapas;
    }

    public function render()
    {
        $search = $this->search;

        $quejas = Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos'])
            ->when($this->filterEstado,   fn($q) => $q->where('estado', $this->filterEstado))
            ->when($this->filterServicio, fn($q) => $q->where('servicio', $this->filterServicio))
            ->when($this->filterTipo,     fn($q) => $q->where('tipo_solicitud', $this->filterTipo))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhere('nombre_actividad', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        // Para cada queja, extraer la fecha de cada etapa del flujo desde los seguimientos
        $quejas->getCollection()->transform(function ($queja) {
            $queja->fecha_revision   = $queja->seguimientos->firstWhere('estado_nuevo', 'en_revision')?->created_at;
            $queja->fecha_derivacion = $queja->seguimientos->firstWhere('estado_nuevo', 'derivado_coordinador')?->created_at;
            $queja->fecha_validacion = $queja->seguimientos->firstWhere('estado_nuevo', 'en_validacion')?->created_at;
            $queja->fecha_respuesta  = $queja->seguimientos->firstWhere('estado_nuevo', 'respondido')?->created_at;
            $queja->fecha_cierre     = $queja->seguimientos
                ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                ->sortByDesc('created_at')
                ->first()?->created_at;
            return $queja;
        });

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->detalleId)
            : null;

        $quejaFlujo = $this->flujoId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->flujoId)
            : null;

        return view('livewire.quejas.historico-flujo', [
            'quejas'       => $quejas,
            'quejaDetalle' => $quejaDetalle,
            'quejaFlujo'   => $quejaFlujo,
            'estados'      => Queja::ESTADOS,
            'servicios'    => Queja::SERVICIOS,
            'tipos'        => Queja::TIPOS,
        ]);
    }
}
