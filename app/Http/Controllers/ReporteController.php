<?php

namespace App\Http\Controllers;

use App\Models\Queja;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ReporteController extends Controller
{
    public function controlFlujoPdf(Request $request): Response
    {
        $user  = Auth::user();
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');

        $servicioJefe = match($user->role) {
            'jefe_formacion'      => 'formacion',
            'jefe_capacitacion'   => 'capacitacion',
            'jefe_administrativo' => 'administrativo',
            default               => null,
        };

        $quejas = Queja::when($user->isJefeUnidad(), function ($q) use ($user, $servicioJefe) {
                        return $servicioJefe
                            ? $q->where('servicio', $servicioJefe)
                            : $q->where('jefe_id', $user->id);
                    })
            ->when($request->query('estado'),   fn($q) => $q->where('estado', $request->query('estado')))
            ->when($request->query('servicio'), fn($q) => $q->where('servicio', $request->query('servicio')))
            ->when($request->query('tipo'),     fn($q) => $q->where('tipo_solicitud', $request->query('tipo')))
            ->when($desde, fn($q) => $q->whereDate('created_at', '>=', $desde))
            ->when($hasta,  fn($q) => $q->whereDate('created_at', '<=', $hasta))
            ->with(['user', 'revisor', 'jefe', 'seguimientos'])
            ->latest()
            ->get();

        $unidadLabel = $servicioJefe
            ? (Queja::SERVICIOS[$servicioJefe] ?? $servicioJefe)
            : 'Todas las unidades';

        $filtros = [
            'desde'   => $desde,
            'hasta'   => $hasta,
            'unidad'  => $unidadLabel,
            'usuario' => $user->name,
        ];

        $pdf = Pdf::loadView('pdf.control-flujo', compact('quejas', 'filtros'))
            ->setPaper('letter', 'landscape');

        return $pdf->download('control-flujo-' . now()->format('Ymd_His') . '.pdf');
    }

    public function solicitudPdf(Queja $queja): Response
    {
        $user = Auth::user();

        $permitido = $user->isSuperusuario() || $user->isSistemas() || $user->isRevisor() || $user->isJefeUnidad();
        if (!$permitido) {
            abort(403);
        }

        $queja->load('user');

        $coloresEstado = [
            'pendiente'            => '#ffc107',
            'en_revision'          => '#17a2b8',
            'derivado_coordinador' => '#007bff',
            'respondido'           => '#6c757d',
            'en_validacion'        => '#6f42c1',
            'resuelto'             => '#28a745',
            'no_procede'           => '#dc3545',
        ];

        $estadoColor = $coloresEstado[$queja->estado] ?? '#6c757d';

        $pdf = Pdf::loadView('pdf.reporte-solicitud', compact('queja', 'estadoColor'))
            ->setPaper('letter', 'portrait');

        $nombre = 'solicitud-' . str_pad($queja->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($nombre);
    }

    public function reporteFinalPdf(Queja $queja): Response|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->hasRole('superusuario', 'sistemas') && !$user->isRevisor() && !$user->isJefeUnidad()) {
            abort(403);
        }

        if (!in_array($queja->estado, ['resuelto', 'no_procede'])) {
            return back()->with('error', 'El reporte final solo está disponible para solicitudes cerradas.');
        }

        $queja->load(['user', 'revisor', 'coordinador', 'jefe', 'seguimientos.user']);

        $seguimientos = $queja->seguimientos;

        $fechaRevision   = $seguimientos->firstWhere('estado_nuevo', 'en_revision')?->created_at;
        $fechaDerivacion = $seguimientos->firstWhere('estado_nuevo', 'derivado_coordinador')?->created_at;
        $fechaRespuesta  = $seguimientos->firstWhere('estado_nuevo', 'respondido')?->created_at;
        $fechaValidacion = $seguimientos->firstWhere('estado_nuevo', 'en_validacion')?->created_at;
        $fechaCierre     = $seguimientos->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                            ->sortByDesc('created_at')->first()?->created_at;

        $pdf = Pdf::loadView('pdf.reporte-final', compact(
            'queja',
            'fechaRevision',
            'fechaDerivacion',
            'fechaRespuesta',
            'fechaValidacion',
            'fechaCierre'
        ))->setPaper('letter', 'portrait');

        $nombre = 'reporte-final-' . str_pad($queja->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($nombre);
    }
}
