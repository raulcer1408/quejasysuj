<?php

namespace App\Livewire\Quejas;

use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AsignarQuejas extends Component
{
    use WithPagination;

    public string $filterEstado   = '';
    public string $filterServicio = '';
    public string $filterTipo     = '';
    public string $search         = '';

    public bool   $showModal      = false;
    public ?int   $quejaId        = null;
    public int    $nuevoRevisorId = 0;
    public string $comentario     = '';

    public bool  $showDetalle = false;
    public ?int  $detalleId   = null;

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

    public function abrirModal(int $id): void
    {
        $this->quejaId        = $id;
        $this->nuevoRevisorId = 0;
        $this->comentario     = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->quejaId   = null;
    }

    public function reasignar(): void
    {
        $this->validate([
            'nuevoRevisorId' => ['required', 'integer', 'min:1', 'exists:users,id'],
            'comentario'     => ['required', 'string', 'min:5'],
        ], [
            'nuevoRevisorId.required' => 'Seleccione un revisor.',
            'nuevoRevisorId.min'      => 'Seleccione un revisor.',
            'comentario.required'     => 'La justificación es obligatoria.',
            'comentario.min'          => 'La justificación debe tener al menos 5 caracteres.',
        ]);

        $queja        = Queja::findOrFail($this->quejaId);
        $nuevoRevisor = User::findOrFail($this->nuevoRevisorId);
        $estadoActual = $queja->estado;

        if (in_array($queja->servicio, Queja::SERVICIOS_DIRECTOS_JEFE)) {
            $queja->update(['jefe_id' => $nuevoRevisor->id, 'revisor_id' => null, 'estado' => 'en_validacion']);
            $accion      = "Reasignada a Jefa Administrativa: {$nuevoRevisor->name}";
            $estadoNuevo = 'en_validacion';
        } else {
            $accion      = "Reasignado a revisor: {$nuevoRevisor->name}";
            $queja->update(['revisor_id' => $nuevoRevisor->id]);
            $estadoNuevo = $estadoActual;
        }

        $queja->seguimientos()->create([
            'user_id'         => Auth::id(),
            'accion'          => $accion,
            'comentario'      => $this->comentario,
            'estado_anterior' => $estadoActual,
            'estado_nuevo'    => $estadoNuevo,
        ]);

        $id     = $queja->id;
        $nombre = $nuevoRevisor->name;
        $this->cerrarModal();
        session()->flash('success', "Solicitud #{$id} asignada a {$nombre}.");
    }

    public function render()
    {
        $search = $this->search;

        $quejas = Queja::with(['user', 'revisor', 'coordinador'])
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

        $revisores = collect();
        if ($this->quejaId) {
            $queja = Queja::find($this->quejaId);
            if ($queja && isset(Queja::REVISOR_POR_SERVICIO[$queja->servicio])) {
                $rolRevisor = Queja::REVISOR_POR_SERVICIO[$queja->servicio];
                $revisores  = User::revisoresParaServicio($rolRevisor)->get();
            }
        }

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->detalleId)
            : null;

        return view('livewire.quejas.asignar-quejas', [
            'quejas'       => $quejas,
            'estados'      => Queja::ESTADOS,
            'servicios'    => Queja::SERVICIOS,
            'tipos'        => Queja::TIPOS,
            'revisores'    => $revisores,
            'quejaDetalle' => $quejaDetalle,
        ]);
    }
}
