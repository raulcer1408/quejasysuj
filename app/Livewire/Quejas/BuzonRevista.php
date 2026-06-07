<?php

namespace App\Livewire\Quejas;

use App\Models\AuditLog;
use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BuzonRevista extends Component
{
    use WithPagination;

    public string $filterEstado = '';
    protected $paginationTheme = 'bootstrap';

    public bool   $showModal  = false;
    public ?int   $quejaId    = null;
    public string $comentario = '';
    public string $accion     = '';
    public int    $jefeUsuarioId = 0;

    // Modal de resolución (miembro_comision)
    public bool   $showResolver = false;
    public ?int   $resolverQuejaId = null;
    public string $resolverComentario = '';

    public bool $showDetalle = false;
    public ?int $detalleId   = null;

    public function abrirAccion(int $id, string $accion): void
    {
        $this->quejaId       = $id;
        $this->accion        = $accion;
        $this->comentario    = '';
        $this->jefeUsuarioId = 0;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal     = false;
        $this->quejaId       = null;
        $this->comentario    = '';
        $this->accion        = '';
        $this->jefeUsuarioId = 0;
    }

    public function abrirResolver(int $id): void
    {
        $this->resolverQuejaId    = $id;
        $this->resolverComentario = '';
        $this->resetValidation();
        $this->showResolver = true;
    }

    public function cerrarResolver(): void
    {
        $this->showResolver       = false;
        $this->resolverQuejaId    = null;
        $this->resolverComentario = '';
    }

    public function aceptar(): void
    {
        $this->validate(
            ['resolverComentario' => ['required', 'string', 'min:5']],
            ['resolverComentario.required' => 'La observación es obligatoria.',
             'resolverComentario.min'      => 'La observación debe tener al menos 5 caracteres.']
        );

        $queja = Queja::findOrFail($this->resolverQuejaId);
        $user  = Auth::user();
        $queja->registrarSeguimiento(
            $user,
            'Solicitud aceptada y resuelta por Comisión Revista',
            'resuelto',
            $this->resolverComentario
        );
        AuditLog::registrar('Solicitud Aceptada', "Solicitud #{$queja->id} resuelta por {$user->name} (Comisión Revista).");

        $this->cerrarResolver();
        session()->flash('success', 'Solicitud marcada como resuelta.');
    }

    public function rechazar(): void
    {
        $this->validate(
            ['resolverComentario' => ['required', 'string', 'min:5']],
            ['resolverComentario.required' => 'La observación es obligatoria.',
             'resolverComentario.min'      => 'La observación debe tener al menos 5 caracteres.']
        );

        $queja = Queja::findOrFail($this->resolverQuejaId);
        $user  = Auth::user();
        $queja->registrarSeguimiento(
            $user,
            'Solicitud rechazada — No Procede por Comisión Revista',
            'no_procede',
            $this->resolverComentario
        );
        AuditLog::registrar('Solicitud Rechazada', "Solicitud #{$queja->id} rechazada por {$user->name} (Comisión Revista).");

        $this->cerrarResolver();
        session()->flash('success', 'Solicitud marcada como No Procede.');
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

    public function procesarAccion(): void
    {
        $rules = ['comentario' => ['required', 'string', 'min:5']];

        if ($this->accion === 'enviar_validacion') {
            $rules['jefeUsuarioId'] = ['required', 'integer', 'min:1', 'exists:users,id'];
        }

        $this->validate($rules, [
            'comentario.required'    => 'El comentario es obligatorio.',
            'comentario.min'         => 'El comentario debe tener al menos 5 caracteres.',
            'jefeUsuarioId.required' => 'Debe seleccionar un Jefe de Unidad.',
            'jefeUsuarioId.min'      => 'Debe seleccionar un Jefe de Unidad.',
        ]);

        $queja = Queja::findOrFail($this->quejaId);
        $user  = Auth::user();

        match ($this->accion) {
            'revisar' => (function () use ($queja, $user) {
                $queja->registrarSeguimiento($user, 'Revisión iniciada', 'en_revision', $this->comentario);
                AuditLog::registrar('Revisión iniciada', "Solicitud #{$queja->id} — {$user->name} inició revisión (Revista).");
            })(),
            'enviar_validacion' => (function () use ($queja, $user) {
                $jefe = User::findOrFail($this->jefeUsuarioId);
                $queja->update(['jefe_id' => $jefe->id]);
                $queja->registrarSeguimiento($user, "Enviado a validación de: {$jefe->name}", 'en_validacion', $this->comentario);
                AuditLog::registrar('Enviado a validación', "Solicitud #{$queja->id} enviada a validación de: {$jefe->name} por {$user->name}.");
            })(),
            'no_procede' => (function () use ($queja, $user) {
                $queja->registrarSeguimiento($user, 'Solicitud marcada como No Procede', 'no_procede', $this->comentario);
                AuditLog::registrar('No Procede', "Solicitud #{$queja->id} marcada como No Procede por {$user->name} (Revista).");
            })(),
        };

        $this->cerrarModal();
        session()->flash('success', 'Acción registrada correctamente.');
    }

    public function render()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->isUsuarioEje() && !$user->isMiembroComision()) {
            abort(403);
        }

        $quejas = Queja::where('servicio', 'investigacion')
            ->when($this->filterEstado, fn($q) => $q->where('estado', $this->filterEstado))
            ->with('user', 'revisor', 'coordinador', 'jefe')
            ->latest()
            ->paginate(10);

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->detalleId)
            : null;

        $resolverQueja = $this->resolverQuejaId
            ? Queja::with(['user', 'seguimientos'])->find($this->resolverQuejaId)
            : null;

        $jefes = User::whereIn('role', User::ROLES_JEFE)
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('livewire.quejas.buzon-revista', [
            'quejas'        => $quejas,
            'estados'       => Queja::ESTADOS,
            'quejaDetalle'  => $quejaDetalle,
            'resolverQueja' => $resolverQueja,
            'jefes'         => $jefes,
            'esMiembro'     => auth()->user()->isMiembroComision(),
        ]);
    }
}
