<?php

namespace App\Livewire\Quejas;

use App\Models\Queja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CoordinadorHistorial extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filterEstado  = '';
    public string $filterTipo    = '';

    public bool $showDetalle = false;
    public ?int $detalleId   = null;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterEstado(): void  { $this->resetPage(); }
    public function updatingFilterTipo(): void    { $this->resetPage(); }

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

    public function render()
    {
        $search = $this->search;

        $userId = Auth::id();

        $quejas = Queja::where(function ($q) use ($userId) {
                // Asignada actualmente como coordinador
                $q->where('coordinador_id', $userId)
                // O participó como coordinador en alguna derivación pasada
                  ->orWhereHas('seguimientos', fn($s) =>
                      $s->where('user_id', $userId)
                        ->where('estado_nuevo', 'respondido')
                  );
            })
            ->when($this->filterEstado, fn($q) => $q->where('estado', $this->filterEstado))
            ->when($this->filterTipo,   fn($q) => $q->where('tipo_solicitud', $this->filterTipo))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhere('nombre_actividad', 'like', "%{$search}%");
                });
            })
            ->with(['user', 'revisor', 'seguimientos'])
            ->latest()
            ->paginate(15);

        $quejas->getCollection()->transform(function ($queja) {
            $queja->fecha_revision   = $queja->seguimientos->firstWhere('estado_nuevo', 'en_revision')?->created_at;
            $queja->fecha_derivacion = $queja->seguimientos->firstWhere('estado_nuevo', 'derivado_coordinador')?->created_at;
            $queja->fecha_respuesta  = $queja->seguimientos->firstWhere('estado_nuevo', 'respondido')?->created_at;
            $queja->fecha_validacion = $queja->seguimientos->firstWhere('estado_nuevo', 'en_validacion')?->created_at;
            $queja->fecha_cierre     = $queja->seguimientos
                ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                ->sortByDesc('created_at')
                ->first()?->created_at;
            return $queja;
        });

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->detalleId)
            : null;

        return view('livewire.quejas.coordinador-historial', [
            'quejas'       => $quejas,
            'quejaDetalle' => $quejaDetalle,
            'estados'      => Queja::ESTADOS,
            'tipos'        => Queja::TIPOS,
        ]);
    }
}
