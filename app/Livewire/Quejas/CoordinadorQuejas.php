<?php

namespace App\Livewire\Quejas;

use App\Models\AuditLog;
use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CoordinadorQuejas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public bool $showModal = false;
    public ?int $quejaId = null;
    public string $comentario = '';

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

    public function responderDesdeDetalle(int $id): void
    {
        $this->cerrarDetalle();
        $this->abrirRespuesta($id);
    }

    public function abrirRespuesta(int $id): void
    {
        $this->quejaId    = $id;
        $this->comentario = '';
        $this->showModal  = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal  = false;
        $this->quejaId    = null;
        $this->comentario = '';
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
        $queja    = Queja::findOrFail($this->quejaId);
        $queja->registrarSeguimiento($user, "Respuesta de {$rolLabel}", 'respondido', $this->comentario);
        AuditLog::registrar('Respuesta emitida', "Solicitud #{$queja->id} respondida por {$user->name} ({$rolLabel}).");

        $this->cerrarModal();
        session()->flash('success', 'Respuesta registrada correctamente.');
    }

    public function render()
    {
        $quejas = Queja::where('coordinador_id', Auth::id())
            ->where('estado', 'derivado_coordinador')
            ->with('user', 'revisor', 'coordinador')
            ->latest()
            ->paginate(10);

        $quejaDetalle = $this->detalleId
            ? Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])->find($this->detalleId)
            : null;

        return view('livewire.quejas.coordinador-quejas', [
            'quejas'       => $quejas,
            'quejaDetalle' => $quejaDetalle,
        ]);
    }
}
