<div>
    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row align-items-end">
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Desde</label>
                    <input wire:model.live="fechaDesde" type="date" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Hasta</label>
                    <input wire:model.live="fechaHasta" type="date" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Servicio</label>
                    @if ($servicioForzado)
                        <input type="text"
                               class="form-control form-control-sm bg-light"
                               value="{{ $servicios[$servicioForzado] ?? $servicioForzado }}"
                               disabled>
                    @else
                        <select wire:model.live="filterServicio" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            @foreach ($servicios as $v => $e)
                                <option value="{{ $v }}">{{ $e }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Umbral días lentas</label>
                    <input wire:model.live="umbralDias" type="number" min="1"
                           class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0 d-flex align-items-end">
                    <button wire:click="$set('fechaDesde',''); $set('fechaHasta',''); $set('filterServicio',''); $set('umbralDias', 5)"
                            class="btn btn-sm btn-secondary w-100">
                        <i class="fas fa-times mr-1"></i>Limpiar
                    </button>
                </div>
                <div class="col-6 col-md-2 d-flex align-items-end" style="gap:4px;">
                    <button wire:click="exportExcel" class="btn btn-sm btn-success flex-fill">
                        <i class="fas fa-file-excel"></i><span class="d-none d-sm-inline ml-1">Excel</span>
                    </button>
                    <button wire:click="exportPdf" class="btn btn-sm btn-danger flex-fill">
                        <i class="fas fa-file-pdf"></i><span class="d-none d-sm-inline ml-1">PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas resumen — móvil --}}
    <div class="row mb-3 d-md-none">
        <div class="col-6 mb-2">
            <div class="card shadow-sm mb-0 border-left border-primary" style="border-left-width:4px!important;">
                <div class="card-body py-2 px-3">
                    <div class="text-primary mb-1" style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Solicitudes cerradas</div>
                    <div style="font-size:1.6rem;font-weight:700;line-height:1;">{{ $datos['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 mb-2">
            <div class="card shadow-sm mb-0 border-left border-info" style="border-left-width:4px!important;">
                <div class="card-body py-2 px-3">
                    <div class="text-info mb-1" style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Promedio atención</div>
                    <div style="font-size:1.6rem;font-weight:700;line-height:1;">{{ $datos['promedio'] }}<small style="font-size:0.9rem;">d</small></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card shadow-sm mb-0 border-left border-success" style="border-left-width:4px!important;">
                <div class="card-body py-2 px-3">
                    <div class="text-success mb-1" style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Rápida / Lenta</div>
                    <div style="font-size:1.3rem;font-weight:700;line-height:1;">{{ $datos['minimo'] }}d / {{ $datos['maximo'] }}d</div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card shadow-sm mb-0 border-left border-warning" style="border-left-width:4px!important;">
                <div class="card-body py-2 px-3">
                    <div class="text-warning mb-1" style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Lentas &gt;{{ $umbralDias }}d</div>
                    <div style="font-size:1.6rem;font-weight:700;line-height:1;">{{ $datos['lentas'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas resumen — escritorio --}}
    <div class="row mb-3 d-none d-md-flex">
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary"><i class="fas fa-clipboard-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Solicitudes cerradas</span>
                    <span class="info-box-number">{{ $datos['total'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Promedio de atención</span>
                    <span class="info-box-number">{{ $datos['promedio'] }} días</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success"><i class="fas fa-tachometer-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Más rápida / Más lenta</span>
                    <span class="info-box-number">{{ $datos['minimo'] }}d / {{ $datos['maximo'] }}d</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Lentas (&gt; {{ $umbralDias }} días)</span>
                    <span class="info-box-number">{{ $datos['lentas'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Promedios --}}
    <div class="row mb-3">
        {{-- Promedio por servicio --}}
        <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card card-outline card-primary h-100">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0" style="font-size:0.85rem;">
                        <i class="fas fa-cogs mr-1"></i>Promedio por Servicio
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if (empty($datos['porServicio']))
                        <p class="text-muted text-center py-3 small">Sin datos.</p>
                    @else
                        @php $maxServ = max($datos['porServicio']); @endphp
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="font-size:0.78rem;">Servicio</th>
                                    <th class="text-center" style="font-size:0.78rem;">Prom.</th>
                                    <th style="font-size:0.78rem;">Barra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datos['porServicio'] as $serv => $prom)
                                    <tr>
                                        <td style="font-size:0.82rem;">{{ $serv }}</td>
                                        <td class="text-center font-weight-bold" style="font-size:0.82rem;">{{ $prom }}d</td>
                                        <td>
                                            @php $pct = $maxServ > 0 ? round(($prom/$maxServ)*100) : 0; @endphp
                                            <div style="background:{{ $prom > $umbralDias ? '#dc3545' : '#007bff' }};height:10px;width:{{ $pct }}%;border-radius:2px;min-width:4px;"></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Promedio por revisor --}}
        <div class="col-12 col-md-8">
            <div class="card card-outline card-info h-100">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0" style="font-size:0.85rem;">
                        <i class="fas fa-user-clock mr-1"></i>Promedio por Revisor
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if (empty($datos['porRevisor']))
                        <p class="text-muted text-center py-3 small">Sin datos.</p>
                    @else
                        @php $maxRev = max($datos['porRevisor']); @endphp
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="font-size:0.78rem;">Revisor</th>
                                    <th class="text-center" style="font-size:0.78rem;">Prom.</th>
                                    <th style="font-size:0.78rem;">Barra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datos['porRevisor'] as $revisor => $prom)
                                    <tr>
                                        <td style="font-size:0.82rem;">{{ $revisor }}</td>
                                        <td class="text-center font-weight-bold" style="font-size:0.82rem;">{{ $prom }}d</td>
                                        <td>
                                            @php $pct = $maxRev > 0 ? round(($prom/$maxRev)*100) : 0; @endphp
                                            <div style="background:{{ $prom > $umbralDias ? '#dc3545' : '#28a745' }};height:10px;width:{{ $pct }}%;border-radius:2px;min-width:4px;"></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detalle por solicitud —— cabecera --}}
    <div class="card card-outline card-danger">
        <div class="card-header py-2">
            <h6 class="card-title mb-0" style="font-size:0.85rem;">
                <i class="fas fa-table mr-1"></i>Detalle por Solicitud
                <span class="badge badge-secondary ml-1">{{ $datos['total'] }}</span>
                @if ($datos['lentas'] > 0)
                    <span class="badge badge-warning ml-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $datos['lentas'] }} lentas
                    </span>
                @endif
            </h6>
        </div>

        {{-- Vista móvil — tarjetas --}}
        <div class="card-body p-2 d-md-none">
            @forelse ($filasPag as $f)
                @php
                    $q     = $f['queja'];
                    $lenta = $f['total'] !== null && $f['total'] > $umbralDias;
                @endphp
                <div class="card mb-2 shadow-sm {{ $lenta ? 'border-warning' : '' }}">
                    <div class="card-body py-2 px-3" style="{{ $lenta ? 'background:#fff8e1;' : '' }}">
                        {{-- Fila 1: ID + tipo + días --}}
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center" style="gap:5px;">
                                <span class="font-weight-bold text-muted" style="font-size:0.78rem;">#{{ str_pad($q->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <span class="badge {{ $q->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}" style="font-size:10px;">
                                    {{ \App\Models\Queja::TIPOS[$q->tipo_solicitud] ?? $q->tipo_solicitud }}
                                </span>
                                <span class="badge badge-{{ $q->colorEstado() }}" style="font-size:10px;">
                                    {{ $q->etiquetaEstado() }}
                                </span>
                            </div>
                            @if ($f['total'] !== null)
                                <span class="badge {{ $f['total'] > $umbralDias ? 'badge-danger' : ($f['total'] > ($umbralDias / 2) ? 'badge-warning' : 'badge-success') }}"
                                      style="font-size:11px;">
                                    {{ $f['total'] }}d
                                    @if ($lenta)<i class="fas fa-exclamation-triangle ml-1"></i>@endif
                                </span>
                            @else
                                <span class="text-muted" style="font-size:0.78rem;">—</span>
                            @endif
                        </div>
                        {{-- Fila 2: solicitante + servicio --}}
                        <div class="mb-1" style="font-size:0.82rem;">
                            <i class="fas fa-user mr-1 text-muted"></i>{{ $q->user->name }}
                            <span class="text-muted ml-2">· {{ \App\Models\Queja::SERVICIOS[$q->servicio] ?? $q->servicio }}</span>
                        </div>
                        {{-- Fila 3: revisor + fechas --}}
                        <div class="d-flex justify-content-between" style="font-size:0.78rem;color:#666;">
                            <span><i class="fas fa-search mr-1"></i>{{ $q->revisor?->name ?? '—' }}</span>
                            <span>
                                {{ $q->created_at->format('d/m/Y') }}
                                @if ($f['cierre'])
                                    → {{ $f['cierre']->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    No hay solicitudes cerradas con los filtros aplicados.
                </div>
            @endforelse
        </div>

        {{-- Vista escritorio — tabla --}}
        <div class="card-body p-0 d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:14px;">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Servicio</th>
                            <th>Solicitante</th>
                            <th>Revisor</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Registro</th>
                            <th class="text-center">Cierre</th>
                            <th class="text-center">Total días</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filasPag as $f)
                            @php
                                $q     = $f['queja'];
                                $lenta = $f['total'] !== null && $f['total'] > $umbralDias;
                            @endphp
                            <tr style="{{ $lenta ? 'background:#fff3cd;' : '' }}">
                                <td class="font-weight-bold">{{ str_pad($q->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <span class="badge {{ $q->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                        {{ \App\Models\Queja::TIPOS[$q->tipo_solicitud] ?? $q->tipo_solicitud }}
                                    </span>
                                </td>
                                <td>{{ \App\Models\Queja::SERVICIOS[$q->servicio] ?? $q->servicio }}</td>
                                <td>{{ $q->user->name }}</td>
                                <td>{{ $q->revisor?->name ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $q->colorEstado() }}">{{ $q->etiquetaEstado() }}</span>
                                </td>
                                <td class="text-center text-muted">{{ $q->created_at->format('d/m/Y') }}</td>
                                <td class="text-center text-muted">{{ $f['cierre']?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($f['total'] !== null)
                                        <span class="badge {{ $f['total'] > $umbralDias ? 'badge-danger' : ($f['total'] > ($umbralDias / 2) ? 'badge-warning' : 'badge-success') }}">
                                            {{ $f['total'] }}d
                                        </span>
                                        @if ($lenta)
                                            <i class="fas fa-exclamation-triangle text-warning ml-1" title="Solicitud lenta"></i>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No hay solicitudes cerradas con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
