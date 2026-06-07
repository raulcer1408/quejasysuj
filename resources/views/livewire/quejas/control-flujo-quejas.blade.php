<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-12 col-md-3 mb-2">
                    <input wire:model.live.debounce.300ms="search" type="text"
                           class="form-control form-control-sm"
                           placeholder="Buscar solicitante o actividad...">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <select wire:model.live="filterEstado" class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <select wire:model.live="filterServicio" class="form-control form-control-sm">
                        <option value="">Todos los servicios</option>
                        @foreach ($servicios as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <select wire:model.live="filterTipo" class="form-control form-control-sm">
                        <option value="">Todos los tipos</option>
                        @foreach ($tipos as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <input wire:model.live="fechaDesde" type="date"
                           class="form-control form-control-sm" title="Desde"
                           placeholder="Desde">
                </div>
                <div class="col-6 col-md-1 mb-2">
                    <input wire:model.live="fechaHasta" type="date"
                           class="form-control form-control-sm" title="Hasta"
                           placeholder="Hasta">
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12 d-flex justify-content-end" style="gap:6px;">
                    <button wire:click="$set('fechaDesde',''); $set('fechaHasta',''); $set('filterEstado',''); $set('filterServicio',''); $set('filterTipo',''); $set('search','')"
                            class="btn btn-sm btn-secondary">
                        <i class="fas fa-times mr-1"></i>Limpiar
                    </button>
                    @if(auth()->user()->isJefeUnidad())
                    <button wire:click="exportPdf" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i>Exportar PDF
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-stream mr-1"></i>Control de Flujo de Solicitudes
        </h6>
        <span class="badge badge-info">{{ $quejas->total() }}</span>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $queja)
            @php
                $segCierre = $queja->seguimientos
                    ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                    ->sortByDesc('created_at')->first();
                $minutos = $segCierre
                    ? (int) $queja->created_at->diffInMinutes($segCierre->created_at)
                    : null;
                if ($minutos === null) {
                    $tiempoLabel = '<span class="badge badge-warning">En curso</span>';
                } elseif ($minutos < 60) {
                    $tiempoLabel = "<span class='badge badge-success'>{$minutos} min</span>";
                } elseif ($minutos < 1440) {
                    $h = intdiv($minutos,60); $m = $minutos%60;
                    $t = $m > 0 ? "{$h}h {$m}min" : "{$h}h";
                    $tiempoLabel = "<span class='badge badge-info'>{$t}</span>";
                } else {
                    $d = intdiv($minutos,1440); $hr = intdiv($minutos%1440,60);
                    $t = $hr > 0 ? "{$d}d {$hr}h" : "{$d}d";
                    $color = $d >= 5 ? 'danger' : ($d >= 2 ? 'warning' : 'primary');
                    $tiempoLabel = "<span class='badge badge-{$color}'>{$t}</span>";
                }
            @endphp
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: ID + tipo + estado --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="text-muted small font-weight-bold">
                                #{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }} ml-1"
                                  style="font-size:10px;">
                                {{ \App\Models\Queja::TIPOS[$queja->tipo_solicitud] ?? $queja->tipo_solicitud }}
                            </span>
                        </div>
                        <span class="badge badge-{{ $queja->colorEstado() }}" style="font-size:10px;">
                            {{ $queja->etiquetaEstado() }}
                        </span>
                    </div>
                    {{-- Fila 2: actividad + solicitante + servicio --}}
                    <div class="mb-2">
                        <div class="font-weight-bold" style="font-size:0.85rem;line-height:1.3;">
                            {{ Str::limit($queja->nombre_actividad, 50) }}
                        </div>
                        <small class="text-muted">
                            {{ $queja->user->name }} ·
                            {{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}
                        </small>
                    </div>
                    {{-- Fila 3: fecha + tiempo + acción --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <small class="text-muted">{{ $queja->created_at->format('d/m/Y') }}</small>
                            {!! $tiempoLabel !!}
                        </div>
                        <button wire:click="verDetalle({{ $queja->id }})"
                                class="btn btn-xs btn-info" title="Ver flujo">
                            <i class="fas fa-project-diagram mr-1"></i>Flujo
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No hay solicitudes en su historial de flujo.
            </div>
        @endforelse
        <div class="mt-2">{{ $quejas->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-primary d-none d-md-block">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0" style="font-size:13px;">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Servicio</th>
                        <th>Actividad</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Registro</th>
                        <th class="text-center">Tiempo total</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quejas as $queja)
                        @php
                            $segCierre = $queja->seguimientos
                                ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                                ->sortByDesc('created_at')->first();
                            $minutos = $segCierre
                                ? (int) $queja->created_at->diffInMinutes($segCierre->created_at)
                                : null;
                            if ($minutos === null) {
                                $tiempoLabel = '<span class="text-muted">En curso</span>';
                            } elseif ($minutos < 60) {
                                $tiempoLabel = "<span class='badge badge-success'>{$minutos} min</span>";
                            } elseif ($minutos < 1440) {
                                $h = intdiv($minutos,60); $m = $minutos%60;
                                $t = $m > 0 ? "{$h}h {$m}min" : "{$h}h";
                                $tiempoLabel = "<span class='badge badge-info'>{$t}</span>";
                            } else {
                                $d = intdiv($minutos,1440); $hr = intdiv($minutos%1440,60);
                                $t = $hr > 0 ? "{$d}d {$hr}h" : "{$d}d";
                                $color = $d >= 5 ? 'danger' : ($d >= 2 ? 'warning' : 'primary');
                                $tiempoLabel = "<span class='badge badge-{$color}'>{$t}</span>";
                            }
                        @endphp
                        <tr>
                            <td class="font-weight-bold">{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $queja->user->name }}</td>
                            <td>
                                <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                    {{ \App\Models\Queja::TIPOS[$queja->tipo_solicitud] ?? $queja->tipo_solicitud }}
                                </span>
                            </td>
                            <td>{{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</td>
                            <td>{{ Str::limit($queja->nombre_actividad, 22) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $queja->colorEstado() }}"
                                      style="white-space:normal;word-break:break-word;display:inline-block;max-width:110px;">
                                    {{ $queja->etiquetaEstado() }}
                                </span>
                            </td>
                            <td class="text-center text-muted small">{{ $queja->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">{!! $tiempoLabel !!}</td>
                            <td class="text-center">
                                <button wire:click="verDetalle({{ $queja->id }})"
                                        class="btn btn-sm btn-info" title="Ver flujo detallado">
                                    <i class="fas fa-project-diagram"></i> Ver flujo
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay solicitudes en su historial de flujo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $quejas->links() }}</div>
    </div>

    {{-- Modal detalle de flujo --}}
    @if ($showDetalle && $quejaDetalle)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-stream mr-2"></i>
                            Flujo — #{{ str_pad($quejaDetalle->id, 5, '0', STR_PAD_LEFT) }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        {{-- Chips de resumen --}}
                        <div class="d-flex flex-wrap mb-3" style="gap:6px;">
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:0.82rem;padding:6px 10px;">
                                {{ \App\Models\Queja::TIPOS[$quejaDetalle->tipo_solicitud] ?? $quejaDetalle->tipo_solicitud }}
                            </span>
                            <span class="badge badge-primary" style="font-size:0.82rem;padding:6px 10px;">
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}"
                                  style="font-size:0.82rem;padding:6px 10px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                            @if ($durTotal)
                                @php
                                    if ($durTotal < 60) { $tStr = $durTotal.' min'; }
                                    elseif ($durTotal < 1440) { $h=intdiv($durTotal,60);$m=$durTotal%60; $tStr=$m>0?"{$h}h {$m}min":"{$h}h"; }
                                    else { $d=intdiv($durTotal,1440);$hr=intdiv($durTotal%1440,60); $tStr=$hr>0?"{$d}d {$hr}h":"{$d}d"; }
                                @endphp
                                <span class="badge badge-success" style="font-size:0.82rem;padding:6px 10px;">
                                    <i class="fas fa-stopwatch mr-1"></i>{{ $tStr }}
                                </span>
                            @else
                                <span class="badge badge-warning" style="font-size:0.82rem;padding:6px 10px;">
                                    <i class="fas fa-stopwatch mr-1"></i>En curso
                                </span>
                            @endif
                        </div>

                        {{-- Involucrados --}}
                        <div class="row mb-3">
                            <div class="col-6 col-md-4 mb-2">
                                <small class="text-muted d-block">Solicitante</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->user->name }}</span>
                            </div>
                            <div class="col-6 col-md-4 mb-2">
                                <small class="text-muted d-block">Revisor</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->revisor?->name ?? '—' }}</span>
                            </div>
                            <div class="col-6 col-md-4 mb-2">
                                <small class="text-muted d-block">Coordinador</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->coordinador?->name ?? '—' }}</span>
                            </div>
                            <div class="col-12 mb-1">
                                <small class="text-muted d-block">Actividad</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->nombre_actividad }}</span>
                            </div>
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">Registrado</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        {{-- Tabla de etapas --}}
                        <h6 class="mb-2" style="font-size:0.9rem;">
                            <i class="fas fa-stream mr-1"></i>Línea de tiempo — tiempo por etapa
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size:12.5px;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:30px">#</th>
                                        <th>Etapa</th>
                                        <th>Actor</th>
                                        <th style="white-space:nowrap;">Fecha y hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($etapas as $i => $etapa)
                                        <tr>
                                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                                            <td>
                                                <span class="badge badge-{{ $etapa['color'] }}"
                                                      style="white-space:normal;word-break:break-word;display:inline-block;font-size:11px;padding:4px 8px;">
                                                    {{ $etapa['nombre'] }}
                                                </span>
                                            </td>
                                            <td style="font-size:12px;">{{ $etapa['actor'] }}</td>
                                            <td style="font-size:12px;font-weight:600;color:#333;white-space:nowrap;">
                                                {{ $etapa['fecha']->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
