<div>
    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           class="form-control form-control-sm"
                           placeholder="Buscar por solicitante o actividad...">
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <select wire:model.live="filterEstado" class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <select wire:model.live="filterServicio" class="form-control form-control-sm">
                        <option value="">Todos los servicios</option>
                        @foreach ($servicios as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select wire:model.live="filterTipo" class="form-control form-control-sm">
                        <option value="">Todos los tipos</option>
                        @foreach ($tipos as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-stream mr-1"></i>Histórico de Flujo
        </h6>
        <span class="badge badge-primary">{{ $quejas->total() }} solicitudes</span>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $item)
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: ID + tipo + estado --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="text-muted small">#{{ $item->id }}</span>
                            <span class="badge {{ $item->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }} ml-1"
                                  style="font-size:10px;">
                                {{ ucfirst($item->tipo_solicitud) }}
                            </span>
                        </div>
                        <span class="badge badge-{{ $item->colorEstado() }}" style="font-size:10px;">
                            {{ $item->etiquetaEstado() }}
                        </span>
                    </div>
                    {{-- Fila 2: solicitante + actividad --}}
                    <div class="mb-2">
                        <div class="font-weight-bold" style="font-size:0.85rem;line-height:1.3;">
                            {{ Str::limit($item->nombre_actividad, 48) }}
                        </div>
                        <small class="text-muted">
                            {{ $item->user->name }} ·
                            {{ \App\Models\Queja::SERVICIOS[$item->servicio] ?? $item->servicio }}
                        </small>
                    </div>
                    {{-- Fila 3: etapas del flujo (mini indicadores) --}}
                    <div class="d-flex flex-wrap mb-2" style="gap:4px;">
                        <span class="badge badge-secondary" style="font-size:9px;">
                            <i class="fas fa-circle mr-1"></i>{{ $item->created_at->format('d/m/Y') }}
                        </span>
                        @if ($item->fecha_revision)
                            <span class="badge badge-info" style="font-size:9px;">
                                <i class="fas fa-search mr-1"></i>{{ $item->fecha_revision->format('d/m/Y') }}
                            </span>
                        @endif
                        @if ($item->fecha_derivacion)
                            <span class="badge badge-primary" style="font-size:9px;">
                                <i class="fas fa-share mr-1"></i>{{ $item->fecha_derivacion->format('d/m/Y') }}
                            </span>
                        @endif
                        @if ($item->fecha_validacion)
                            <span class="badge" style="font-size:9px;background:#6f42c1;color:#fff;">
                                <i class="fas fa-check-double mr-1"></i>{{ $item->fecha_validacion->format('d/m/Y') }}
                            </span>
                        @endif
                        @if ($item->fecha_cierre)
                            <span class="badge badge-{{ $item->estado === 'resuelto' ? 'success' : 'danger' }}"
                                  style="font-size:9px;">
                                <i class="fas fa-flag-checkered mr-1"></i>{{ $item->fecha_cierre->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                    {{-- Acciones --}}
                    <div class="d-flex justify-content-end" style="gap:4px;">
                        <button wire:click="verDetalle({{ $item->id }})"
                                class="btn btn-xs btn-info" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button wire:click="verFlujo({{ $item->id }})"
                                class="btn btn-xs btn-primary" title="Ver flujo completo">
                            <i class="fas fa-stream"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No se encontraron solicitudes.
            </div>
        @endforelse
        <div class="mt-2">{{ $quejas->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-primary d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" style="font-size:0.82rem;">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Solicitante</th>
                            <th>Tipo</th>
                            <th>Servicio</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Registro</th>
                            <th class="text-center">Revisión</th>
                            <th class="text-center">Derivación</th>
                            <th class="text-center">Validación</th>
                            <th class="text-center">Respuesta</th>
                            <th class="text-center">Cierre</th>
                            <th class="text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quejas as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>
                                    <span class="badge {{ $item->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                        {{ ucfirst($item->tipo_solicitud) }}
                                    </span>
                                </td>
                                <td>{{ \App\Models\Queja::SERVICIOS[$item->servicio] ?? $item->servicio }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $item->colorEstado() }}">
                                        {{ $item->etiquetaEstado() }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    @if ($item->fecha_revision)
                                        <span class="text-info">{{ $item->fecha_revision->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_derivacion)
                                        <span class="text-primary">{{ $item->fecha_derivacion->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_validacion)
                                        <span style="color:#6f42c1;">{{ $item->fecha_validacion->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_respuesta)
                                        <span class="text-secondary">{{ $item->fecha_respuesta->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_cierre)
                                        <span class="text-{{ $item->estado === 'resuelto' ? 'success' : 'danger' }}">
                                            {{ $item->fecha_cierre->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <button wire:click="verDetalle({{ $item->id }})"
                                            class="btn btn-sm btn-info mr-1" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button wire:click="verFlujo({{ $item->id }})"
                                            class="btn btn-sm btn-primary" title="Ver control de flujo">
                                        <i class="fas fa-stream"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No se encontraron solicitudes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $quejas->links() }}</div>
    </div>

    {{-- Modal detalle del flujo --}}
    @if ($showDetalle && $quejaDetalle)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-stream mr-2"></i>Flujo — Solicitud #{{ $quejaDetalle->id }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- Chips --}}
                        <div class="d-flex flex-wrap mb-3" style="gap:6px;">
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:0.82rem;padding:6px 10px;">
                                {{ ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-secondary" style="font-size:0.82rem;padding:6px 10px;">
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}"
                                  style="font-size:0.82rem;padding:6px 10px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-1">
                                <small class="text-muted d-block">Solicitante</small>
                                <span style="font-size:0.9rem;">{{ $quejaDetalle->user->name }}</span>
                            </div>
                            <div class="col-12 col-md-6 mb-1">
                                <small class="text-muted d-block">Actividad</small>
                                <span style="font-size:0.9rem;">{{ $quejaDetalle->nombre_actividad }}</span>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mb-2" style="font-size:0.9rem;">
                            <i class="fas fa-stream mr-1"></i>Línea de tiempo del flujo
                        </h6>
                        @forelse ($quejaDetalle->seguimientos->sortBy('created_at') as $seg)
                            <div class="d-flex mb-3">
                                <div class="mr-2 text-center" style="min-width:50px;">
                                    <small class="text-muted d-block" style="font-size:0.72rem;">
                                        {{ $seg->created_at->format('d/m/Y') }}
                                    </small>
                                    <small class="text-muted d-block" style="font-size:0.72rem;">
                                        {{ $seg->created_at->format('H:i') }}
                                    </small>
                                </div>
                                <div class="border-left border-primary pl-2 flex-grow-1 pb-2">
                                    <div class="d-flex flex-wrap align-items-center mb-1" style="gap:4px;">
                                        <span class="badge badge-{{ \App\Models\Queja::COLORES_ESTADO[$seg->estado_nuevo] ?? 'secondary' }}"
                                              style="font-size:10px;">
                                            {{ \App\Models\Queja::ESTADOS[$seg->estado_nuevo] ?? $seg->estado_nuevo }}
                                        </span>
                                        <small class="text-muted">por <strong>{{ $seg->user->name }}</strong></small>
                                    </div>
                                    <p class="mb-0 small">{{ $seg->accion }}</p>
                                    @if ($seg->comentario)
                                        <p class="text-muted small mb-0 mt-1 border-left border-secondary pl-2">
                                            {{ $seg->comentario }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">Sin movimientos registrados.</p>
                        @endforelse
                    </div>
                    <div class="modal-footer flex-column flex-sm-row align-items-stretch align-items-sm-center">
                        @if (in_array($quejaDetalle->estado, ['resuelto', 'no_procede']))
                            <a href="{{ route('quejas.reporte-final', $quejaDetalle->id) }}"
                               target="_blank" class="btn btn-danger mb-2 mb-sm-0 mr-sm-auto">
                                <i class="fas fa-file-pdf mr-1"></i>Reporte Final
                            </a>
                        @endif
                        <button type="button" class="btn btn-secondary" wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Control de Flujo --}}
    @if ($showFlujo && $quejaFlujo)
        @php $etapas = $this->calcularEtapas($quejaFlujo); @endphp
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-stream mr-2"></i>
                            Control de Flujo — #{{ str_pad($quejaFlujo->id, 5, '0', STR_PAD_LEFT) }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarFlujo">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-1">
                                <small class="text-muted d-block">Solicitante</small>
                                <span style="font-size:0.9rem;">{{ $quejaFlujo->user->name }}</span>
                            </div>
                            <div class="col-12 col-md-6 mb-1">
                                <small class="text-muted d-block">Actividad</small>
                                <span style="font-size:0.9rem;">{{ $quejaFlujo->nombre_actividad }}</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size:12.5px;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:30px">#</th>
                                        <th>Etapa</th>
                                        <th>Actor</th>
                                        <th>Fecha y hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($etapas as $i => $etapa)
                                        <tr>
                                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                                            <td>
                                                <span class="badge badge-{{ $etapa['color'] }}"
                                                      style="white-space:normal;word-break:break-word;display:inline-block;font-size:11px;padding:3px 7px;">
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
                        <button type="button" class="btn btn-secondary" wire:click="cerrarFlujo">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
