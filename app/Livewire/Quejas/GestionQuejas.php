<?php

namespace App\Livewire\Quejas;

use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class GestionQuejas extends Component
{
    use WithPagination;

    public string $filterEstado   = '';
    public string $filterServicio = '';
    public string $filterTipo     = '';
    public string $search         = '';

    public bool   $showDetalle  = false;
    public ?int   $detalleId    = null;

    public bool   $showEliminar   = false;
    public ?int   $eliminarId     = null;

    public bool   $showAsignar    = false;
    public ?int   $asignarId      = null;
    public int    $nuevoRevisorId = 0;
    public string $comentario     = '';

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

    public function confirmarEliminar(int $id): void
    {
        $this->eliminarId   = $id;
        $this->showEliminar = true;
    }

    public function cerrarEliminar(): void
    {
        $this->showEliminar = false;
        $this->eliminarId   = null;
    }

    public function eliminar(): void
    {
        $queja = Queja::findOrFail($this->eliminarId);

        if ($queja->respaldo) {
            Storage::disk('public')->delete($queja->respaldo);
        }

        $id = $queja->id;
        $queja->delete();

        $this->cerrarEliminar();
        session()->flash('success', "Solicitud #{$id} eliminada correctamente.");
    }

    public function abrirAsignar(int $id): void
    {
        $this->asignarId      = $id;
        $this->nuevoRevisorId = 0;
        $this->comentario     = '';
        $this->resetValidation();
        $this->showAsignar = true;
    }

    public function cerrarAsignar(): void
    {
        $this->showAsignar = false;
        $this->asignarId   = null;
    }

    public function asignar(): void
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

        $queja        = Queja::findOrFail($this->asignarId);
        $nuevoRevisor = User::findOrFail($this->nuevoRevisorId);
        $estadoActual = $queja->estado;

        if (in_array($queja->servicio, Queja::SERVICIOS_DIRECTOS_JEFE)) {
            $queja->update(['jefe_id' => $nuevoRevisor->id, 'revisor_id' => null, 'estado' => 'en_validacion']);
            $accion      = "Asignada directamente a Jefa Administrativa: {$nuevoRevisor->name}";
            $estadoNuevo = 'en_validacion';
        } else {
            $accion = $queja->revisor_id
                ? "Revisor reasignado a: {$nuevoRevisor->name}"
                : "Asignado a revisor: {$nuevoRevisor->name}";
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
        $this->cerrarAsignar();
        session()->flash('success', "Solicitud #{$id}: asignada a {$nombre}.");
    }

    public function render()
    {
        $search = $this->search;

        $quejas = Queja::with(['user', 'revisor', 'coordinador', 'jefe'])
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

        $revisores = collect();
        if ($this->asignarId) {
            $quejaAsignar = Queja::find($this->asignarId);
            if ($quejaAsignar && isset(Queja::REVISOR_POR_SERVICIO[$quejaAsignar->servicio])) {
                $rolRevisor = Queja::REVISOR_POR_SERVICIO[$quejaAsignar->servicio];
                $revisores  = User::revisoresParaServicio($rolRevisor)->get();
            }
        }

        return view('livewire.quejas.gestion-quejas', [
            'quejas'       => $quejas,
            'estados'      => Queja::ESTADOS,
            'servicios'    => Queja::SERVICIOS,
            'tipos'        => Queja::TIPOS,
            'quejaDetalle' => $quejaDetalle,
            'revisores'    => $revisores,
        ]);
    }
}
