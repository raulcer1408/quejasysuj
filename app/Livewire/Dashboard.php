<?php

namespace App\Livewire;

use App\Models\Queja;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        $total     = $this->query($user)->count();
        $enProceso = $this->query($user)->whereNotIn('estado', ['resuelto', 'no_procede'])->count();
        $resueltas = $this->query($user)->where('estado', 'resuelto')->count();
        $noProcede = $this->query($user)->where('estado', 'no_procede')->count();

        $porTipo = $this->query($user)
            ->select('tipo_solicitud', DB::raw('count(*) as total'))
            ->groupBy('tipo_solicitud')
            ->pluck('total', 'tipo_solicitud');

        $porServicio = $this->query($user)
            ->select('servicio', DB::raw('count(*) as total'))
            ->groupBy('servicio')
            ->pluck('total', 'servicio');

        $mesesEs = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $meses   = collect(range(5, 0))->map(fn($i) => now()->subMonths($i));

        $tendenciaRaw = $this->query($user)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($r) => "{$r->year}-{$r->month}");

        $tendencia = $meses->map(fn($m) => [
            'label' => $mesesEs[$m->month - 1] . ' ' . $m->year,
            'total' => $tendenciaRaw->get("{$m->year}-{$m->month}")?->total ?? 0,
        ]);

        $porEstado = $this->query($user)
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('livewire.dashboard', compact(
            'total', 'enProceso', 'resueltas', 'noProcede',
            'porTipo', 'porServicio', 'tendencia', 'porEstado'
        ));
    }

    private function query(User $user): Builder
    {
        $q = Queja::query();

        if ($user->hasRole('superusuario', 'sistemas')) {
            return $q;
        }
        if ($user->isJefeUnidad()) {
            return $q->where(function ($sub) use ($user) {
                $sub->where('jefe_id', $user->id)
                    ->orWhere('coordinador_id', $user->id);
            });
        }
        if ($user->isRevisor()) {
            return $q->where('revisor_id', $user->id);
        }
        if ($user->isCoordinador()) {
            return $q->where('coordinador_id', $user->id);
        }

        return $q->where('user_id', $user->id);
    }
}
