<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           class="form-control form-control-sm"
                           placeholder="Buscar solicitante o actividad...">
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
                <div class="col-6 col-md-0 d-flex d-md-none align-items-center justify-content-end">
                    <span class="badge badge-primary">{{ $quejas->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-clipboard-list mr-1"></i>Todas las Solicitudes
        </h6>
        <span class="badge badge-primary d-none d-md-inline">{{ $quejas->total() }} solicitudes</span>
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
                    <div class="mb-1">
                        <div class="font-weight-bold" style="font-size:0.85rem;line-height:1.3;">
                            {{ Str::limit($item->nombre_actividad, 50) }}
                        </div>
                        <div class="text-muted" style="font-size:0.76rem;">
                            {{ $item->user->name }}
                        </div>
                    </div>
                    {{-- Fila 3: servicio + fecha + acciones --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge badge-secondary" style="font-size:10px;">
                                {{ \App\Models\Queja::SERVICIOS[$item->servicio] ?? $item->servicio }}
                            </span>
                            <small class="text-muted ml-1">{{ $item->created_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="d-flex" style="gap:4px;">
                            <button wire:click="verDetalle({{ $item->id }})"
                                    class="btn btn-xs btn-info" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if (!in_array($item->estado, ['resuelto', 'no_procede']))
                                <button wire:click="abrirAsignar({{ $item->id }})"
                                        class="btn btn-xs btn-warning text-white"
                                        title="{{ $item->revisor_id ? 'Reasignar' : 'Asignar' }}">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                            @endif
                            <button wire:click="confirmarEliminar({{ $item->id }})"
                                    class="btn btn-xs btn-danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
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
            <table class="table table-hover table-striped mb-0" style="font-size:0.875rem;">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Servicio</th>
                        <th>Actividad</th>
                        <th class="text-center">Estado</th>
                        <th>Revisor</th>
                        <th>Coordinador / Derivado</th>
                        <th>Jefe de Unidad</th>
                        <th>Fecha</th>
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
                            <td>{{ Str::limit($item->nombre_actividad, 22) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item->colorEstado() }}">
                                    {{ $item->etiquetaEstado() }}
                                </span>
                            </td>
                            <td>{{ $item->revisor?->name ?? '—' }}</td>
                            <td>{{ $item->coordinador?->name ?? '—' }}</td>
                            <td>{{ $item->jefe?->name ?? '—' }}</td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td class="text-center text-nowrap">
                                <button wire:click="verDetalle({{ $item->id }})"
                                        class="btn btn-sm btn-info mr-1" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if (!in_array($item->estado, ['resuelto', 'no_procede']))
                                    <button wire:click="abrirAsignar({{ $item->id }})"
                                            class="btn btn-sm btn-warning text-white mr-1"
                                            title="{{ $item->revisor_id ? 'Reasignar' : 'Asignar' }}">
                                        <i class="fas fa-user-edit"></i>
                                    </button>
                                @endif
                                <button wire:click="confirmarEliminar({{ $item->id }})"
                                        class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No se encontraron solicitudes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $quejas->links() }}</div>
    </div>

    {{-- Modal detalle --}}
    @if ($showDetalle && $quejaDetalle)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-file-alt mr-2"></i>Solicitud #{{ $quejaDetalle->id }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- Chips de estado --}}
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

                        {{-- Involucrados --}}
                        <div class="row mb-3">
                            <div class="col-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Solicitante</small>
                                <span style="font-size:0.85rem;">{{ $quejaDetalle->user->name }}</span>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Revisor</small>
                                <span style="font-size:0.85rem;">{{ $quejaDetalle->revisor?->name ?? '—' }}</span>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Coordinador</small>
                                <span style="font-size:0.85rem;">{{ $quejaDetalle->coordinador?->name ?? '—' }}</span>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Jefe de Unidad</small>
                                <span style="font-size:0.85rem;">{{ $quejaDetalle->jefe?->name ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Fecha:</small>
                            {{ $quejaDetalle->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="mb-3">
                            <strong>Actividad:</strong>
                            <p class="mb-0" style="font-size:0.9rem;">{{ $quejaDetalle->nombre_actividad }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Descripción:</strong>
                            <p class="mt-1 text-muted border-left border-info pl-3 mb-0"
                               style="font-size:0.9rem;white-space:pre-wrap;">{{ $quejaDetalle->descripcion }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Respaldo:</strong>
                            @if ($quejaDetalle->respaldo)
                                <div class="mt-1">
                                    <a href="{{ Storage::url($quejaDetalle->respaldo) }}" target="_blank"
                                       class="btn btn-sm btn-outline-danger mr-1">
                                        <i class="fas fa-eye mr-1"></i>Ver
                                    </a>
                                    <a href="{{ Storage::url($quejaDetalle->respaldo) }}" download
                                       class="btn btn-sm btn-danger">
                                        <i class="fas fa-download mr-1"></i>Descargar
                                    </a>
                                </div>
                            @else
                                <span class="text-muted ml-1">Sin respaldo</span>
                            @endif
                        </div>

                        {{-- Historial --}}
                        <hr>
                        <h6 class="mb-2"><i class="fas fa-history mr-1"></i>Historial</h6>
                        @forelse ($quejaDetalle->seguimientos as $seg)
                            <div class="mb-2 p-2 border-left border-primary pl-3"
                                 style="border-left-width:3px !important;">
                                <small class="text-muted d-block">
                                    {{ $seg->created_at->format('d/m/Y H:i') }} · <strong>{{ $seg->user->name }}</strong>
                                </small>
                                <p class="mb-0" style="font-size:0.88rem;">{{ $seg->accion }}</p>
                                @if ($seg->comentario)
                                    <p class="text-muted small mb-0">{{ $seg->comentario }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small">Sin movimientos aún.</p>
                        @endforelse
                    </div>
                    <div class="modal-footer flex-column flex-sm-row align-items-stretch align-items-sm-center">
                        <a href="{{ route('quejas.reporte-pdf', $quejaDetalle->id) }}"
                           target="_blank"
                           class="btn btn-outline-danger mb-2 mb-sm-0 mr-sm-auto">
                            <i class="fas fa-file-pdf mr-1"></i>Formulario
                        </a>
                        @if (in_array($quejaDetalle->estado, ['resuelto', 'no_procede']))
                            <a href="{{ route('quejas.reporte-final', $quejaDetalle->id) }}"
                               target="_blank"
                               class="btn btn-danger mb-2 mb-sm-0">
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

    {{-- Modal eliminar --}}
    @if ($showEliminar)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-trash mr-2"></i>Eliminar Solicitud #{{ $eliminarId }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarEliminar">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">¿Está seguro de que desea eliminar esta solicitud?</p>
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Esta acción eliminará permanentemente la solicitud y su historial. <strong>No se puede deshacer.</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarEliminar">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="eliminar"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="eliminar">
                                <i class="fas fa-trash mr-1"></i>Eliminar
                            </span>
                            <span wire:loading wire:target="eliminar">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Eliminando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal asignación --}}
    @if ($showAsignar)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-user-edit mr-2"></i>Asignar Revisor — #{{ $asignarId }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarAsignar">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Revisor <span class="text-danger">*</span></label>
                            <select wire:model="nuevoRevisorId"
                                    class="form-control @error('nuevoRevisorId') is-invalid @enderror">
                                <option value="0">-- Seleccionar revisor --</option>
                                @foreach ($revisores as $revisor)
                                    <option value="{{ $revisor->id }}">
                                        {{ $revisor->name }}
                                        @if ($revisor->unidad)
                                            ({{ \App\Models\User::UNIDADES[$revisor->unidad] ?? $revisor->unidad }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('nuevoRevisorId')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @if ($revisores->isEmpty())
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    No hay revisores activos disponibles.
                                </small>
                            @endif
                        </div>
                        <div class="form-group mb-0">
                            <label>Justificación <span class="text-danger">*</span></label>
                            <textarea wire:model="comentario"
                                      class="form-control @error('comentario') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Motivo de la asignación..."></textarea>
                            @error('comentario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarAsignar">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning text-white" wire:click="asignar"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="asignar">
                                <i class="fas fa-user-edit mr-1"></i>Confirmar
                            </span>
                            <span wire:loading wire:target="asignar">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Guardando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
