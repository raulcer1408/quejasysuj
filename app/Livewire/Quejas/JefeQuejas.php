<?php

namespace App\Livewire\Quejas;

use App\Models\AuditLog;
use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class JefeQuejas extends Component
{
    use WithPagination;

    public string $filterEstado = '';
    public string $filterServicio = '';
    protected $paginationTheme = 'bootstrap';

    public bool $showModal = false;
    public ?int $quejaId = null;
    public string $comentario = '';
    public string $accion = '';
    public int $derivarUsuarioId = 0;

    public bool $showDetalle = false;
    public ?int $detalleId   = null;

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

    public function abrirAccion(int $id, string $accion): void
    {
        $this->quejaId         = $id;
        $this->detalleId       = $id;
        $this->accion          = $accion;
        $this->comentario      = '';
        $this->derivarUsuarioId = 0;
        $this->resetValidation();
        $this->showModal       = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal        = false;
        $this->quejaId          = null;
        $this->detalleId        = null;
        $this->comentario       = '';
        $this->accion           = '';
        $this->derivarUsuarioId = 0;
    }

    public function procesarAccion(): void
    {
        $rules = ['comentario' => ['required', 'string', 'min:5']];

        if ($this->accion === 'derivar') {
            $rules['derivarUsuarioId'] = ['required', 'integer', 'min:1', 'exists:users,id'];
        }

        $this->validate($rules, [
            'comentario.required'       => 'El comentario es obligatorio.',
            'comentario.min'            => 'El comentario debe tener al menos 5 caracteres.',
            'derivarUsuarioId.required' => 'Debe seleccionar a quién derivar.',
            'derivarUsuarioId.min'      => 'Debe seleccionar a quién derivar.',
        ]);

        $queja = Queja::findOrFail($this->quejaId);
        $user  = Auth::user();

        if ($this->accion === 'derivar') {
            $destinatario = User::findOrFail($this->derivarUsuarioId);
            $queja->update(['coordinador_id' => $destinatario->id, 'jefe_id' => $user->id]);
            $queja->registrarSeguimiento($user, "Derivado por Jefe a: {$destinatario->name}", 'derivado_coordinador', $this->comentario);
            AuditLog::registrar('Derivación por Jefe', "Solicitud #{$queja->id} derivada por {$user->name} a: {$destinatario->name}.");
            $this->cerrarModal();
            session()->flash('success', 'Solicitud derivada correctamente.');
            return;
        }

        $queja->registrarSeguimiento($user, 'Validado por Jefe de Unidad', 'en_validacion', $this->comentario);
        AuditLog::registrar('Validación', "Solicitud #{$queja->id} validada por {$user->name}.");

        $this->cerrarModal();
        session()->flash('success', 'Acción registrada correctamente.');
    }

    public function aceptar(): void
    {
        $this->validate([
            'comentario' => ['required', 'string', 'min:5'],
        ], [
            'comentario.required' => 'La resolución es obligatoria.',
            'comentario.min'      => 'La resolución debe tener al menos 5 caracteres.',
        ]);

        $queja = Queja::findOrFail($this->quejaId);
        $user  = Auth::user();
        $queja->registrarSeguimiento($user, 'Solicitud aceptada y resuelta por Jefe de Unidad', 'resuelto', $this->comentario);
        AuditLog::registrar('Solicitud Aceptada', "Solicitud #{$queja->id} resuelta/aceptada por {$user->name}.");

        $this->cerrarModal();
        session()->flash('success', 'Solicitud marcada como resuelta correctamente.');
    }

    public function rechazar(): void
    {
        $this->validate([
            'comentario' => ['required', 'string', 'min:5'],
        ], [
            'comentario.required' => 'La resolución es obligatoria.',
            'comentario.min'      => 'La resolución debe tener al menos 5 caracteres.',
        ]);

        $queja = Queja::findOrFail($this->quejaId);
        $user  = Auth::user();
        $queja->registrarSeguimiento($user, 'Solicitud rechazada — No Procede por Jefe de Unidad', 'no_procede', $this->comentario);
        AuditLog::registrar('Solicitud Rechazada', "Solicitud #{$queja->id} rechazada/No Procede por {$user->name}.");

        $this->cerrarModal();
        session()->flash('success', 'Solicitud marcada como No Procede.');
    }

    public function render()
    {
        $jefeId = Auth::id();

        $quejas = Queja::where(function ($q) use ($jefeId) {
                $q->whereIn('estado', ['en_validacion', 'respondido', 'resuelto'])
                  ->where('jefe_id', $jefeId);
            })
            ->orWhere(function ($q) use ($jefeId) {
                $q->where('estado', 'derivado_coordinador')
                  ->where('coordinador_id', $jefeId);
            })
            ->when($this->filterEstado, fn($q) => $q->where('estado', $this->filterEstado))
            ->when($this->filterServicio, fn($q) => $q->where('servicio', $this->filterServicio))
            ->with('user', 'revisor', 'coordinador', 'jefe')
            ->latest()
            ->paginate(10);

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->detalleId)
            : null;

        $usuariosDerivacion = User::where('activo', true)
            ->whereIn('role', [
                User::ROLE_COORDINADOR_ACADEMICO,
                User::ROLE_COORDINADOR,
                User::ROLE_DOCENTE_INTERNO,
                User::ROLE_DOCENTE,
                User::ROLE_SISTEMAS,
            ])
            ->orderBy('name')
            ->get();

        return view('livewire.quejas.jefe-quejas', [
            'quejas'              => $quejas,
            'estados'             => Queja::ESTADOS,
            'servicios'           => Queja::SERVICIOS,
            'quejaDetalle'        => $quejaDetalle,
            'usuariosDerivacion'  => $usuariosDerivacion,
        ]);
    }
}
