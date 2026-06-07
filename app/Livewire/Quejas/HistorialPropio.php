<?php

namespace App\Livewire\Quejas;

use App\Models\Queja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialPropio extends Component
{
    use WithPagination;

    public string $filterEstado   = '';
    public string $filterServicio = '';
    public string $filterTipo     = '';
    public string $search         = '';

    public bool $showDetalle = false;
    public ?int $detalleId   = null;

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

    public function render()
    {
        $user   = Auth::user();

        if ($user->isVisitante()) {
            abort(403);
        }

        $search = $this->search;

        $query = Queja::with(['user', 'revisor', 'coordinador', 'jefe']);

        if ($user->isSuperusuario() || $user->isSistemas()) {
            // Sin restricción — ven todas las solicitudes
        } elseif ($user->isJefeUnidad()) {
            // Jefe: todas las de su unidad (asignadas a él)
            $query->where('jefe_id', $user->id);
        } elseif ($user->isRevisor()) {
            // Pedagoga / revisora: las que revisó
            $query->where('revisor_id', $user->id);
        } elseif ($user->isCoordinador()) {
            // Coordinador: solo las que le derivaron
            $query->where('coordinador_id', $user->id);
        } elseif ($user->isDocente()) {
            // Docente: las que envió + las que atendió como coordinador
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('coordinador_id', $user->id);
            });
        } else {
            // Visitante u otro rol: solo las propias
            $query->where('user_id', $user->id);
        }

        $quejas = $query
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

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->detalleId)
            : null;

        return view('livewire.quejas.historial-propio', [
            'quejas'       => $quejas,
            'estados'      => Queja::ESTADOS,
            'servicios'    => Queja::SERVICIOS,
            'tipos'        => Queja::TIPOS,
            'quejaDetalle' => $quejaDetalle,
        ]);
    }
}
