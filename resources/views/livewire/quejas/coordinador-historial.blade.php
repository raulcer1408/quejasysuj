<div>
    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           class="form-control form-control-sm"
                           placeholder="Buscar por solicitante o actividad...">
                </div>
                <div class="col-6 col-md-4 mb-2 mb-md-0">
                    <select wire:model.live="filterEstado" class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
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
            <i class="fas fa-history mr-1"></i>Historial de Solicitudes Derivadas
        </h6>
        <span class="badge badge-primary">{{ $quejas->total() }}</span>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $item)
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: ID + tipo + estado --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex flex-wrap" style="gap:3px;">
                            <span class="text-muted small">#{{ $item->id }}</span>
                            <span class="badge {{ $item->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:10px;">
                                {{ ucfirst($item->tipo_solicitud) }}
                            </span>
                        </div>
                        <span class="badge badge-{{ $item->colorEstado() }}" style="font-size:10px;">
                            {{ $item->etiquetaEstado() }}
                        </span>
                    </div>
                    {{-- Fila 2: solicitante --}}
                    <div class="font-weight-bold mb-2" style="font-size:0.85rem;">
                        {{ $item->user->name }}
                    </div>
                    {{-- Fila 3: línea de fechas del flujo --}}
                    <div class="d-flex flex-wrap mb-2" style="gap:3px;">
                        <span class="badge badge-secondary" style="font-size:9px;" title="Registro">
                            <i class="fas fa-circle mr-1"></i>{{ $item->created_at->format('d/m') }}
                        </span>
                        @if ($item->fecha_revision)
                            <span class="badge badge-info" style="font-size:9px;" title="Revisión">
                                <i class="fas fa-search mr-1"></i>{{ $item->fecha_revision->format('d/m') }}
                            </span>
                        @endif
                        @if ($item->fecha_derivacion)
                            <span class="badge badge-primary" style="font-size:9px;" title="Derivación">
                                <i class="fas fa-share mr-1"></i>{{ $item->fecha_derivacion->format('d/m') }}
                            </span>
                        @endif
                        @if ($item->fecha_respuesta)
                            <span class="badge badge-secondary" style="font-size:9px;" title="Respuesta">
                                <i class="fas fa-reply mr-1"></i>{{ $item->fecha_respuesta->format('d/m') }}
                            </span>
                        @endif
                        @if ($item->fecha_validacion)
                            <span class="badge" style="font-size:9px;background:#6f42c1;color:#fff;" title="Validación">
                                <i class="fas fa-check-double mr-1"></i>{{ $item->fecha_validacion->format('d/m') }}
                            </span>
                        @endif
                        @if ($item->fecha_cierre)
                            <span class="badge badge-{{ $item->estado === 'resuelto' ? 'success' : 'danger' }}"
                                  style="font-size:9px;" title="Cierre">
                                <i class="fas fa-flag-checkered mr-1"></i>{{ $item->fecha_cierre->format('d/m') }}
                            </span>
                        @endif
                    </div>
                    {{-- Acción --}}
                    <div class="d-flex justify-content-end">
                        <button wire:click="verDetalle({{ $item->id }})"
                                class="btn btn-xs btn-info" title="Ver detalle del flujo">
                            <i class="fas fa-eye mr-1"></i>Ver flujo
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No se encontraron solicitudes derivadas.
            </div>
        @endforelse
        <div class="mt-2">{{ $quejas->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-primary d-none d-md-block">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>Historial de Solicitudes Derivadas
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $quejas->total() }} solicitudes</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" style="font-size:0.82rem;">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Solicitante</th>
                            <th>Tipo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Registro</th>
                            <th class="text-center">Revisión</th>
                            <th class="text-center">Derivación</th>
                            <th class="text-center">Respuesta</th>
                            <th class="text-center">Validación</th>
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
                                <td class="text-center">
                                    <span class="badge badge-{{ $item->colorEstado() }}">
                                        {{ $item->etiquetaEstado() }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    @if ($item->fecha_revision)
                                        <span class="text-info" title="{{ $item->fecha_revision->format('d/m/Y H:i') }}">
                                            {{ $item->fecha_revision->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_derivacion)
                                        <span class="text-primary" title="{{ $item->fecha_derivacion->format('d/m/Y H:i') }}">
                                            {{ $item->fecha_derivacion->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_respuesta)
                                        <span class="text-secondary" title="{{ $item->fecha_respuesta->format('d/m/Y H:i') }}">
                                            {{ $item->fecha_respuesta->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_validacion)
                                        <span style="color:#6f42c1;" title="{{ $item->fecha_validacion->format('d/m/Y H:i') }}">
                                            {{ $item->fecha_validacion->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->fecha_cierre)
                                        <span class="text-{{ $item->estado === 'resuelto' ? 'success' : 'danger' }}"
                                              title="{{ $item->fecha_cierre->format('d/m/Y H:i') }}">
                                            {{ $item->fecha_cierre->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="verDetalle({{ $item->id }})"
                                            class="btn btn-sm btn-info" title="Ver detalle del flujo">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No se encontraron solicitudes derivadas.
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
                    <div class="modal-header bg-primary text-white py-2">
                        <h5 class="modal-title" style="font-size:0.95rem;">
                            <i class="fas fa-stream mr-2"></i>Flujo completo — Solicitud #{{ $quejaDetalle->id }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-2 p-md-3">
                        {{-- Datos básicos --}}
                        <div class="d-flex flex-wrap mb-3" style="gap:5px;">
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:0.8rem;padding:5px 8px;">
                                {{ ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}"
                                  style="font-size:0.8rem;padding:5px 8px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>
                        <div class="row mb-3" style="font-size:0.85rem;">
                            <div class="col-6 mb-1">
                                <small class="text-muted d-block">Solicitante</small>
                                {{ $quejaDetalle->user->name }}
                            </div>
                            <div class="col-6 mb-1">
                                <small class="text-muted d-block">Servicio</small>
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </div>
                            <div class="col-12 mb-1">
                                <small class="text-muted d-block">Actividad</small>
                                {{ $quejaDetalle->nombre_actividad }}
                            </div>
                        </div>

                        <hr class="my-2">

                        {{-- Timeline de seguimientos --}}
                        <h6 class="mb-3" style="font-size:0.88rem;">
                            <i class="fas fa-stream mr-1"></i>Línea de tiempo del flujo
                        </h6>
                        @forelse ($quejaDetalle->seguimientos->sortBy('created_at') as $seg)
                            <div class="d-flex mb-3">
                                <div class="mr-2 text-center" style="min-width:40px;">
                                    <small class="text-muted d-block" style="font-size:0.68rem;line-height:1.3;">{{ $seg->created_at->format('d/m') }}</small>
                                    <small class="text-muted d-block" style="font-size:0.68rem;line-height:1.3;">{{ $seg->created_at->format('H:i') }}</small>
                                </div>
                                <div class="border-left border-primary pl-2 flex-grow-1 pb-2">
                                    <div class="d-flex align-items-center flex-wrap mb-1" style="gap:4px;">
                                        <span class="badge badge-{{ \App\Models\Queja::COLORES_ESTADO[$seg->estado_nuevo] ?? 'secondary' }}"
                                              style="font-size:10px;">
                                            {{ \App\Models\Queja::ESTADOS[$seg->estado_nuevo] ?? $seg->estado_nuevo }}
                                        </span>
                                        <small class="text-muted" style="font-size:0.75rem;">por <strong>{{ $seg->user->name }}</strong></small>
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
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm w-100 w-md-auto"
                                wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
