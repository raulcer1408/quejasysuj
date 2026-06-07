<?php

namespace App\Livewire\Admin;

use App\Exports\AtencionIndividualExport;
use App\Models\Queja;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ReporteAtencionIndividual extends Component
{
    use WithPagination;

    public string $fechaDesde     = '';
    public string $fechaHasta     = '';
    public string $filterServicio = '';
    public int    $filterUserId   = 0;

    protected $paginationTheme = 'bootstrap';

    public function updatingFechaDesde(): void     { $this->resetPage(); }
    public function updatingFechaHasta(): void     { $this->resetPage(); }
    public function updatingFilterServicio(): void { $this->resetPage(); }
    public function updatingFilterUserId(): void   { $this->resetPage(); }

    /**
     * Calcula las intervenciones de cada actor en cada queja.
     * Retorna un array de ['user', 'queja', 'rol_en_flujo', 'recibido_en', 'respondio_en', 'horas', 'dias']
     */
    private function servicioForzado(): ?string
    {
        $user = auth()->user();
        return match($user->role) {
            'jefe_formacion'      => 'formacion',
            'jefe_capacitacion'   => 'capacitacion',
            'jefe_administrativo' => 'administrativo',
            default               => null,
        };
    }

    private function rolParaMostrar(?User $user, string $rolFlujo): string
    {
        if (!$user) return $rolFlujo;
        if ($user->isVisitante()) return User::roles()[$user->role] ?? 'Visitante';
        return $rolFlujo;
    }

    public function calcularIntervenciones(): array
    {
        $servicioForzado = $this->servicioForzado();

        $quejas = Queja::with(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user'])
            ->when($servicioForzado,                         fn($q) => $q->where('servicio', $servicioForzado))
            ->when(!$servicioForzado && $this->filterServicio, fn($q) => $q->where('servicio', $this->filterServicio))
            ->when($this->fechaDesde,                        fn($q) => $q->whereDate('created_at', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,                        fn($q) => $q->whereDate('created_at', '<=', $this->fechaHasta))
            ->get();

        $intervenciones = [];

        foreach ($quejas as $queja) {
            $segs = $queja->seguimientos->sortBy('created_at')->values();

            // ── Revisor: desde en_revision hasta su siguiente acción ──────────
            $segRevision = $segs->firstWhere('estado_nuevo', 'en_revision');
            if ($segRevision) {
                $siguiente = $segs->first(fn($s) =>
                    $s->created_at > $segRevision->created_at &&
                    in_array($s->estado_nuevo, ['derivado_coordinador', 'en_validacion', 'no_procede'])
                );
                if ($siguiente) {
                    $horas = round($segRevision->created_at->diffInMinutes($siguiente->created_at) / 60, 1);
                    $this->agregarIntervencion($intervenciones, [
                        'user'        => $segRevision->user,
                        'queja'       => $queja,
                        'rol'         => $this->rolParaMostrar($segRevision->user, 'Revisor'),
                        'recibido_en' => $segRevision->created_at,
                        'respondio_en'=> $siguiente->created_at,
                        'horas'       => $horas,
                        'dias'        => round($horas / 24, 1),
                    ]);
                }
            }

            // ── Coordinador: desde derivado_coordinador hasta respondido ──────
            $segDerivado = $segs->firstWhere('estado_nuevo', 'derivado_coordinador');
            $segRespondido = $segs->firstWhere('estado_nuevo', 'respondido');
            if ($segDerivado && $segRespondido && $segRespondido->created_at > $segDerivado->created_at) {
                $horas = round($segDerivado->created_at->diffInMinutes($segRespondido->created_at) / 60, 1);
                $this->agregarIntervencion($intervenciones, [
                    'user'        => $segRespondido->user,
                    'queja'       => $queja,
                    'rol'         => $this->rolParaMostrar($segRespondido->user, 'Coordinador'),
                    'recibido_en' => $segDerivado->created_at,
                    'respondio_en'=> $segRespondido->created_at,
                    'horas'       => $horas,
                    'dias'        => round($horas / 24, 1),
                ]);
            }

            // ── Jefe: desde en_validacion hasta resuelto/no_procede ───────────
            $segValidacion = $segs->firstWhere('estado_nuevo', 'en_validacion');
            $segCierre = $segs->first(fn($s) =>
                in_array($s->estado_nuevo, ['resuelto', 'no_procede']) &&
                ($segValidacion ? $s->created_at > $segValidacion->created_at : true)
            );
            if ($segValidacion && $segCierre) {
                $horas = round($segValidacion->created_at->diffInMinutes($segCierre->created_at) / 60, 1);
                $this->agregarIntervencion($intervenciones, [
                    'user'        => $segCierre->user,
                    'queja'       => $queja,
                    'rol'         => $this->rolParaMostrar($segCierre->user, 'Jefe de Unidad'),
                    'recibido_en' => $segValidacion->created_at,
                    'respondio_en'=> $segCierre->created_at,
                    'horas'       => $horas,
                    'dias'        => round($horas / 24, 1),
                ]);
            }
        }

        // Filtrar por usuario si está seleccionado
        if ($this->filterUserId > 0) {
            $intervenciones = array_filter($intervenciones,
                fn($i) => $i['user']->id === $this->filterUserId
            );
        }

        // Ordenar por usuario y luego por fecha
        usort($intervenciones, fn($a, $b) =>
            strcmp($a['user']->name, $b['user']->name) ?: $a['recibido_en']->timestamp - $b['recibido_en']->timestamp
        );

        return array_values($intervenciones);
    }

    private function agregarIntervencion(array &$arr, array $item): void
    {
        $arr[] = $item;
    }

    public function resumenPorUsuario(array $intervenciones): array
    {
        $grupos = [];
        foreach ($intervenciones as $i) {
            $uid = $i['user']->id;
            if (!isset($grupos[$uid])) {
                $grupos[$uid] = [
                    'user'         => $i['user'],
                    'intervenciones'=> 0,
                    'horas_total'  => 0,
                    'horas_min'    => PHP_FLOAT_MAX,
                    'horas_max'    => 0,
                    'roles'        => [],
                ];
            }
            $grupos[$uid]['intervenciones']++;
            $grupos[$uid]['horas_total'] += $i['horas'];
            $grupos[$uid]['horas_min']    = min($grupos[$uid]['horas_min'], $i['horas']);
            $grupos[$uid]['horas_max']    = max($grupos[$uid]['horas_max'], $i['horas']);
            if (!in_array($i['rol'], $grupos[$uid]['roles'])) {
                $grupos[$uid]['roles'][] = $i['rol'];
            }
        }

        foreach ($grupos as &$g) {
            $g['promedio_horas'] = $g['intervenciones'] > 0
                ? round($g['horas_total'] / $g['intervenciones'], 1) : 0;
            $g['promedio_dias']  = round($g['promedio_horas'] / 24, 1);
            $g['horas_min']      = $g['horas_min'] === PHP_FLOAT_MAX ? 0 : $g['horas_min'];
        }

        usort($grupos, fn($a, $b) => $b['promedio_horas'] <=> $a['promedio_horas']);
        return array_values($grupos);
    }

    public function exportExcel()
    {
        $intervenciones = $this->calcularIntervenciones();
        $resumen        = $this->resumenPorUsuario($intervenciones);
        $filtros        = [
            'desde'    => $this->fechaDesde,
            'hasta'    => $this->fechaHasta,
            'servicio' => $this->filterServicio,
        ];
        return Excel::download(
            new AtencionIndividualExport($intervenciones, $resumen, $filtros),
            'reporte-atencion-individual-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $intervenciones = $this->calcularIntervenciones();
        $resumen        = $this->resumenPorUsuario($intervenciones);
        $filtros        = [
            'desde'    => $this->fechaDesde,
            'hasta'    => $this->fechaHasta,
            'servicio' => $this->filterServicio,
        ];

        $pdf = Pdf::loadView('pdf.reporte-atencion-individual',
            compact('intervenciones', 'resumen', 'filtros'))
            ->setPaper('letter', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-atencion-individual-' . now()->format('Ymd-His') . '.pdf');
    }

    public function render()
    {
        $intervenciones = $this->calcularIntervenciones();
        $resumen        = $this->resumenPorUsuario($intervenciones);
        $usuarios       = User::whereIn('role', array_merge(
            User::ROLES_REVISORES,
            User::ROLES_JEFE,
            [User::ROLE_COORDINADOR_ACADEMICO, User::ROLE_COORDINADOR, User::ROLE_SISTEMAS]
        ))->where('activo', true)->orderBy('name')->get();

        $filasPag = collect($intervenciones)->forPage($this->getPage(), 20);

        return view('livewire.admin.reporte-atencion-individual', [
            'resumen'         => $resumen,
            'filasPag'        => $filasPag,
            'total'           => count($intervenciones),
            'servicios'       => Queja::SERVICIOS,
            'usuarios'        => $usuarios,
            'servicioForzado' => $this->servicioForzado(),
        ]);
    }
}
