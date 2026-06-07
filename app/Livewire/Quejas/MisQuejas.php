<?php

namespace App\Livewire\Quejas;

use App\Models\AuditLog;
use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MisQuejas extends Component
{
    use WithPagination;

    public string $filterEstado = '';
    protected $paginationTheme = 'bootstrap';

    public bool $showDetalle = false;
    public ?int $quejaId = null;

    public bool $showModal    = false;
    public ?int $responderQuejaId = null;
    public string $comentario = '';

    public function verDetalle(int $id): void
    {
        $this->quejaId     = $id;
        $this->showDetalle = true;
    }

    public function cerrarDetalle(): void
    {
        $this->showDetalle = false;
        $this->quejaId     = null;
    }

    public function abrirRespuesta(int $id): void
    {
        $this->responderQuejaId = $id;
        $this->comentario       = '';
        $this->showModal        = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal        = false;
        $this->responderQuejaId = null;
        $this->comentario       = '';
    }

    public function responder(): void
    {
        $this->validate([
            'comentario' => ['required', 'string', 'min:5'],
        ], [
            'comentario.required' => 'La respuesta es obligatoria.',
            'comentario.min'      => 'La respuesta debe tener al menos 5 caracteres.',
        ]);

        $user     = Auth::user();
        $rolLabel = User::roles()[$user->role] ?? ucfirst($user->role);
        $queja    = Queja::findOrFail($this->responderQuejaId);

        abort_if($queja->coordinador_id !== $user->id, 403);

        $queja->registrarSeguimiento($user, "Respuesta de {$rolLabel}", 'respondido', $this->comentario);
        AuditLog::registrar('Respuesta emitida', "Solicitud #{$queja->id} respondida por {$user->name} ({$rolLabel}).");

        $this->cerrarModal();
        session()->flash('success', 'Respuesta registrada correctamente.');
    }

    public function render()
    {
        $user = Auth::user();

        if ($user->isDocente()) {
            $query = Queja::where(function ($q) use ($user) {
                // Propias activas
                $q->where(function ($sub) use ($user) {
                    $sub->where('user_id', $user->id)
                        ->whereNotIn('estado', ['resuelto', 'no_procede']);
                })
                // Derivadas pendientes de respuesta
                ->orWhere(function ($sub) use ($user) {
                    $sub->where('coordinador_id', $user->id)
                        ->where('estado', 'derivado_coordinador');
                });
            });
        } else {
            // Visitante — todas sus propias quejas
            $query = Queja::where('user_id', $user->id);
        }

        $quejas = $query
            ->when($this->filterEstado, fn($q) => $q->where('estado', $this->filterEstado))
            ->with(['user', 'coordinador'])
            ->latest()
            ->paginate(10);

        $quejaDetalle = $this->quejaId
            ? Queja::with('seguimientos.user', 'revisor', 'coordinador', 'jefe')->find($this->quejaId)
            : null;

        $quejaResponder = $this->responderQuejaId
            ? Queja::find($this->responderQuejaId)
            : null;

        return view('livewire.quejas.mis-quejas', [
            'quejas'         => $quejas,
            'estados'        => Queja::ESTADOS,
            'quejaDetalle'   => $quejaDetalle,
            'quejaResponder' => $quejaResponder,
        ]);
    }
}
