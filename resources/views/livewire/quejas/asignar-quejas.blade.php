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
                <div class="col-6 col-md-0 d-flex d-md-none align-items-center justify-content-end">
                    <span class="badge badge-primary">{{ $quejas->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-user-edit mr-1"></i>Asignación de Revisores
        </h6>
        <span class="badge badge-primary d-none d-md-inline">{{ $quejas->total() }} solicitudes</span>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $queja)
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: ID + tipo + estado --}}
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="d-flex flex-wrap" style="gap:3px;">
                            <span class="text-muted small">#{{ $queja->id }}</span>
                            <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:10px;">
                                {{ ucfirst($queja->tipo_solicitud) }}
                            </span>
                        </div>
                        <span class="badge badge-{{ $queja->colorEstado() }}" style="font-size:10px;">
                            {{ $queja->etiquetaEstado() }}
                        </span>
                    </div>
                    {{-- Fila 2: actividad + solicitante·servicio --}}
                    <div class="mb-1">
                        <div class="font-weight-bold" style="font-size:0.85rem;line-height:1.3;">
                            {{ Str::limit($queja->nombre_actividad, 50) }}
                        </div>
                        <small class="text-muted">
                            {{ $queja->user->name }} ·
                            {{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}
                        </small>
                    </div>
                    {{-- Fila 3: revisor + fecha + acciones --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if ($queja->revisor)
                                <span class="text-success" style="font-size:0.75rem;">
                                    <i class="fas fa-user-check mr-1"></i>{{ $queja->revisor->name }}
                                </span>
                            @else
                                <span class="text-danger" style="font-size:0.75rem;">
                                    <i class="fas fa-user-times mr-1"></i>Sin asignar
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center" style="gap:4px;">
                            <small class="text-muted">{{ $queja->created_at->format('d/m/Y') }}</small>
                            <button wire:click="verDetalle({{ $queja->id }})"
                                    class="btn btn-xs btn-info" title="Vista previa">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if (!in_array($queja->estado, ['resuelto', 'no_procede']))
                                <button wire:click="abrirModal({{ $queja->id }})"
                                        class="btn btn-xs btn-warning" title="Reasignar revisor">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                            @endif
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
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Asignación de Revisores</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $quejas->total() }} solicitudes</span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Servicio</th>
                        <th>Actividad</th>
                        <th class="text-center">Estado</th>
                        <th>Revisor Asignado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quejas as $queja)
                        <tr>
                            <td>{{ $queja->id }}</td>
                            <td>{{ $queja->user->name }}</td>
                            <td>
                                <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                    {{ ucfirst($queja->tipo_solicitud) }}
                                </span>
                            </td>
                            <td>{{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</td>
                            <td>{{ Str::limit($queja->nombre_actividad, 25) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $queja->colorEstado() }}">
                                    {{ $queja->etiquetaEstado() }}
                                </span>
                            </td>
                            <td>
                                @if ($queja->revisor)
                                    <span class="text-success">
                                        <i class="fas fa-user-check mr-1"></i>{{ $queja->revisor->name }}
                                    </span>
                                @else
                                    <span class="text-danger">
                                        <i class="fas fa-user-times mr-1"></i>Sin asignar
                                    </span>
                                @endif
                            </td>
                            <td>{{ $queja->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <button wire:click="verDetalle({{ $queja->id }})"
                                        class="btn btn-sm btn-info mr-1" title="Vista previa">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if (!in_array($queja->estado, ['resuelto', 'no_procede']))
                                    <button wire:click="abrirModal({{ $queja->id }})"
                                            class="btn btn-sm btn-warning"
                                            title="Reasignar revisor">
                                        <i class="fas fa-user-edit"></i> Reasignar
                                    </button>
                                @else
                                    <span class="badge badge-{{ $queja->colorEstado() }}">
                                        {{ $queja->etiquetaEstado() }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
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

    {{-- Modal vista previa --}}
    @if ($showDetalle && $quejaDetalle)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white py-2">
                        <h5 class="modal-title" style="font-size:0.95rem;">
                            <i class="fas fa-file-alt mr-2"></i>Solicitud #{{ $quejaDetalle->id }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-2 p-md-3">
                        {{-- Chips --}}
                        <div class="d-flex flex-wrap mb-3" style="gap:5px;">
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:0.8rem;padding:5px 8px;">
                                {{ ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-secondary" style="font-size:0.8rem;padding:5px 8px;">
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}"
                                  style="font-size:0.8rem;padding:5px 8px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>

                        {{-- Datos --}}
                        <div class="row mb-3" style="font-size:0.85rem;">
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Solicitante</small>
                                {{ $quejaDetalle->user->name }}
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Fecha</small>
                                {{ $quejaDetalle->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Revisor asignado</small>
                                @if ($quejaDetalle->revisor)
                                    <span class="text-success">
                                        <i class="fas fa-user-check mr-1"></i>{{ $quejaDetalle->revisor->name }}
                                    </span>
                                @else
                                    <span class="text-danger">Sin asignar</span>
                                @endif
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Jefe de Unidad</small>
                                {{ $quejaDetalle->jefe?->name ?? '—' }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted d-block">Actividad</small>
                            <span style="font-size:0.9rem;">{{ $quejaDetalle->nombre_actividad }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Descripción</small>
                            <p class="border-left border-info pl-3 mb-0"
                               style="font-size:0.88rem;white-space:pre-wrap;color:#444;">{{ $quejaDetalle->descripcion }}</p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Respaldo adjunto</small>
                            @if ($quejaDetalle->respaldo)
                                <div class="d-flex" style="gap:6px;">
                                    <a href="{{ Storage::url($quejaDetalle->respaldo) }}" target="_blank"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-eye mr-1"></i>Ver
                                    </a>
                                    <a href="{{ Storage::url($quejaDetalle->respaldo) }}" download
                                       class="btn btn-sm btn-danger">
                                        <i class="fas fa-download mr-1"></i>Descargar
                                    </a>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:0.88rem;">Sin respaldo adjunto</span>
                            @endif
                        </div>

                        <hr class="my-2">
                        <h6 class="mb-2" style="font-size:0.88rem;">
                            <i class="fas fa-history mr-1"></i>Historial de seguimiento
                        </h6>
                        @forelse ($quejaDetalle->seguimientos as $seg)
                            <div class="mb-2 p-2 border-left border-primary pl-3">
                                <small class="text-muted d-block">
                                    {{ $seg->created_at->format('d/m/Y H:i') }} — <strong>{{ $seg->user->name }}</strong>
                                </small>
                                <p class="mb-0" style="font-size:0.85rem;">{{ $seg->accion }}</p>
                                @if ($seg->comentario)
                                    <p class="text-muted small mb-0">{{ $seg->comentario }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small">Sin movimientos aún.</p>
                        @endforelse
                    </div>
                    <div class="modal-footer py-2 d-flex flex-column flex-sm-row" style="gap:6px;">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                        @if (!in_array($quejaDetalle->estado, ['resuelto', 'no_procede']))
                            <button type="button" class="btn btn-warning btn-sm text-white"
                                    wire:click="cerrarDetalle(); $nextTick(() => $wire.abrirModal({{ $quejaDetalle->id }}))">
                                <i class="fas fa-user-edit mr-1"></i>Reasignar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal reasignación --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-user-edit mr-2"></i>Reasignar Revisor — #{{ $quejaId }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nuevo Revisor <span class="text-danger">*</span></label>
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
                                    No hay revisores activos disponibles para el servicio de esta solicitud.
                                </small>
                            @endif
                        </div>
                        <div class="form-group mb-0">
                            <label>Justificación <span class="text-danger">*</span></label>
                            <textarea wire:model="comentario"
                                      class="form-control @error('comentario') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Motivo de la reasignación..."></textarea>
                            @error('comentario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer py-2 d-flex flex-column flex-sm-row" style="gap:6px;">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="cerrarModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning btn-sm text-white" wire:click="reasignar"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="reasignar">
                                <i class="fas fa-user-edit mr-1"></i>Confirmar Reasignación
                            </span>
                            <span wire:loading wire:target="reasignar">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Guardando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
