<?php

namespace App\Livewire\Quejas;

use App\Models\AuditLog;
use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RevisionQuejas extends Component
{
    use WithPagination;

    public string $filterEstado = '';
    protected $paginationTheme = 'bootstrap';

    public bool $showModal = false;
    public ?int $quejaId = null;
    public string $comentario = '';
    public string $accion = '';
    public int $derivarUsuarioId  = 0;
    public int $jefeUsuarioId     = 0;
    public int $jefeDerivacionId  = 0;

    public bool $showDetalle = false;
    public ?int $detalleId   = null;

    public function abrirAccion(int $id, string $accion): void
    {
        $this->quejaId          = $id;
        $this->accion           = $accion;
        $this->comentario       = '';
        $this->derivarUsuarioId = 0;
        $this->jefeUsuarioId    = 0;
        $this->jefeDerivacionId = 0;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal        = false;
        $this->quejaId          = null;
        $this->comentario       = '';
        $this->accion           = '';
        $this->derivarUsuarioId = 0;
        $this->jefeUsuarioId    = 0;
        $this->jefeDerivacionId = 0;
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
        $rules = [
            'comentario' => ['required', 'string', 'min:5'],
        ];

        if ($this->accion === 'derivar_coordinador') {
            $rules['derivarUsuarioId'] = ['required', 'integer', 'min:1', 'exists:users,id'];
        }

        if ($this->accion === 'enviar_validacion') {
            $rules['jefeUsuarioId'] = ['required', 'integer', 'min:1', 'exists:users,id'];
        }

        $this->validate($rules, [
            'comentario.required'       => 'El comentario es obligatorio.',
            'comentario.min'            => 'El comentario debe tener al menos 5 caracteres.',
            'derivarUsuarioId.required' => 'Debe seleccionar a quién derivar.',
            'derivarUsuarioId.min'      => 'Debe seleccionar a quién derivar.',
            'jefeUsuarioId.required'    => 'Debe seleccionar un Jefe de Unidad.',
            'jefeUsuarioId.min'         => 'Debe seleccionar un Jefe de Unidad.',
        ]);

        $queja = Queja::findOrFail($this->quejaId);
        $user  = Auth::user();

        if ($this->accion === 'derivar_coordinador' && !$user->isSuperusuario() && !$user->isSistemas()) {
            session()->flash('error', 'Solo el Jefe de Unidad puede derivar solicitudes.');
            $this->cerrarModal();
            return;
        }

        match ($this->accion) {
            'revisar' => (function () use ($queja, $user) {
                $queja->registrarSeguimiento($user, 'Revisión iniciada', 'en_revision', $this->comentario);
                AuditLog::registrar('Revisión iniciada', "Solicitud #{$queja->id} — {$user->name} inició revisión.");
            })(),
            'derivar_coordinador' => (function () use ($queja, $user) {
                $destinatario = User::findOrFail($this->derivarUsuarioId);
                $queja->update(['coordinador_id' => $destinatario->id]);
                $queja->registrarSeguimiento($user, "Derivado a: {$destinatario->name}", 'derivado_coordinador', $this->comentario);
                AuditLog::registrar('Derivación', "Solicitud #{$queja->id} derivada a: {$destinatario->name} por {$user->name}.");
            })(),
            'enviar_validacion' => (function () use ($queja, $user) {
                $jefe = User::findOrFail($this->jefeUsuarioId);
                $queja->update(['jefe_id' => $jefe->id]);
                $queja->registrarSeguimiento($user, "Enviado a validación de: {$jefe->name}", 'en_validacion', $this->comentario);
                AuditLog::registrar('Enviado a validación', "Solicitud #{$queja->id} enviada a validación de: {$jefe->name} por {$user->name}.");
            })(),
            'no_procede' => (function () use ($queja, $user) {
                $queja->registrarSeguimiento($user, 'Solicitud marcada como No Procede', 'no_procede', $this->comentario);
                AuditLog::registrar('No Procede', "Solicitud #{$queja->id} marcada como No Procede por {$user->name}.");
            })(),
        };

        $this->cerrarModal();
        session()->flash('success', 'Acción registrada correctamente.');
    }

    public function render()
    {
        $user = Auth::user();

        if ($user->isUsuarioEje() && !$user->isMiembroComision()) {
            abort(403);
        }

        $quejas = Queja::query()
            ->where('servicio', '!=', 'investigacion')
            ->when(
                !$user->isSuperusuario() && !$user->isSistemas(),
                function ($q) use ($user) {
                    $q->where('revisor_id', $user->id)
                      ->where(function ($sub) {
                          // La revisora actúa en estos estados; si ya hay jefe asignado
                          // y la solicitud está en respondido/en_validacion/resuelto/no_procede,
                          // el jefe de unidad es quien debe gestionarla.
                          $sub->whereIn('estado', ['pendiente', 'en_revision', 'derivado_coordinador'])
                              ->orWhere(function ($sub2) {
                                  // respondido sin jefe: la revisora aún debe enviar a validación
                                  $sub2->where('estado', 'respondido')->whereNull('jefe_id');
                              });
                      });
                }
            )
            ->when($this->filterEstado, fn($q) => $q->where('estado', $this->filterEstado))
            ->with('user', 'coordinador')
            ->latest()
            ->paginate(10);

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'seguimientos.user'])->find($this->detalleId)
            : null;

        $usuariosDerivacion = ($user->isSuperusuario() || $user->isSistemas())
            ? User::where('activo', true)
                ->where('id', '!=', $user->id)
                ->whereNotIn('role', [User::ROLE_VISITANTE, User::ROLE_SUPERUSUARIO])
                ->orderBy('name')
                ->get()
            : collect();

        $jefes = User::whereIn('role', [User::ROLE_JEFE_FORMACION, User::ROLE_JEFE_CAPACITACION])
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('livewire.quejas.revision-quejas', [
            'quejas'             => $quejas,
            'estados'            => Queja::ESTADOS,
            'quejaDetalle'       => $quejaDetalle,
            'usuariosDerivacion' => $usuariosDerivacion,
            'jefes'              => $jefes,
        ]);
    }
}
