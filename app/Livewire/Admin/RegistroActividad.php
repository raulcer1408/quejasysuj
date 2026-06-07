<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\QuejaSeguimiento;
use Livewire\Component;
use Livewire\WithPagination;

class RegistroActividad extends Component
{
    use WithPagination;

    public string $tab          = 'sistema';
    public string $search       = '';
    public string $fechaDesde   = '';
    public string $fechaHasta   = '';
    public string $searchNumero = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingFechaDesde(): void    { $this->resetPage(); }
    public function updatingFechaHasta(): void    { $this->resetPage(); }
    public function updatingSearchNumero(): void  { $this->resetPage(); }

    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        if ($this->tab === 'sistema') {
            $registros = AuditLog::with('user')
                ->when($this->search, fn($q) =>
                    $q->whereHas('user', fn($u) =>
                        $u->where('name', 'like', "%{$this->search}%")
                    )->orWhere('descripcion', 'like', "%{$this->search}%")
                )
                ->when($this->fechaDesde, fn($q) =>
                    $q->whereDate('created_at', '>=', $this->fechaDesde)
                )
                ->when($this->fechaHasta, fn($q) =>
                    $q->whereDate('created_at', '<=', $this->fechaHasta)
                )
                ->latest()
                ->paginate(20);
        } else {
            $registros = QuejaSeguimiento::with(['user', 'queja'])
                ->when($this->searchNumero, fn($q) =>
                    $q->where('queja_id', (int) $this->searchNumero)
                )
                ->when($this->search, fn($q) =>
                    $q->whereHas('user', fn($u) =>
                        $u->where('name', 'like', "%{$this->search}%")
                    )->orWhere('accion', 'like', "%{$this->search}%")
                )
                ->when($this->fechaDesde, fn($q) =>
                    $q->whereDate('created_at', '>=', $this->fechaDesde)
                )
                ->when($this->fechaHasta, fn($q) =>
                    $q->whereDate('created_at', '<=', $this->fechaHasta)
                )
                ->latest()
                ->paginate(20);
        }

        return view('livewire.admin.registro-actividad', compact('registros'));
    }
}
