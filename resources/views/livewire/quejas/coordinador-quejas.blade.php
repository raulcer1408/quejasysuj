<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    {{-- Contador --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small"><i class="fas fa-clock mr-1"></i>Solicitudes pendientes de respuesta</span>
                <span class="badge badge-primary">{{ $quejas->total() }}</span>
            </div>
        </div>
    </div>

    {{-- Cabecera --}}
    <div class="mb-2">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-inbox mr-1"></i>Buzón de Quejas Pendientes
        </h6>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $queja)
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: ID + tipo + estado --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="text-muted small">#{{ $queja->id }}</span>
                            <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }} ml-1"
                                  style="font-size:10px;">
                                {{ ucfirst($queja->tipo_solicitud) }}
                            </span>
                        </div>
                        <span class="badge badge-{{ $queja->colorEstado() }}" style="font-size:10px;">
                            {{ $queja->etiquetaEstado() }}
                        </span>
                    </div>
                    {{-- Fila 2: actividad + solicitante --}}
                    <div class="mb-1">
                        <div class="font-weight-bold" style="font-size:0.85rem;line-height:1.3;">
                            {{ Str::limit($queja->nombre_actividad, 50) }}
                        </div>
                        <small class="text-muted">
                            {{ $queja->user->name }}
                            @if ($queja->revisor)
                                · Revisor: {{ $queja->revisor->name }}
                            @endif
                        </small>
                    </div>
                    {{-- Fila 3: fecha + acciones --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">{{ $queja->created_at->format('d/m/Y') }}</small>
                        <div class="d-flex" style="gap:4px;">
                            <button wire:click="verDetalle({{ $queja->id }})"
                                    class="btn btn-xs btn-info" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if ($queja->estado === 'derivado_coordinador')
                                <button wire:click="abrirRespuesta({{ $queja->id }})"
                                        class="btn btn-xs btn-success">
                                    <i class="fas fa-reply mr-1"></i>Responder
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No hay solicitudes derivadas.
            </div>
        @endforelse
        <div class="mt-2">{{ $quejas->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-primary d-none d-md-block">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Revisor</th>
                        <th>Tipo</th>
                        <th>Actividad</th>
                        <th class="text-center">Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quejas as $queja)
                        <tr>
                            <td>{{ $queja->id }}</td>
                            <td>{{ $queja->user->name }}</td>
                            <td>{{ $queja->revisor?->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                    {{ ucfirst($queja->tipo_solicitud) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($queja->nombre_actividad, 25) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $queja->colorEstado() }}">
                                    {{ $queja->etiquetaEstado() }}
                                </span>
                            </td>
                            <td>{{ $queja->created_at->format('d/m/Y') }}</td>
                            <td class="text-center text-nowrap">
                                <button wire:click="verDetalle({{ $queja->id }})"
                                        class="btn btn-sm btn-info mr-1" title="Vista preliminar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if ($queja->estado === 'derivado_coordinador')
                                    <button wire:click="abrirRespuesta({{ $queja->id }})"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-reply mr-1"></i>Responder
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay solicitudes derivadas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $quejas->links() }}</div>
    </div>

    {{-- Modal vista preliminar --}}
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
                                {{ \App\Models\Queja::TIPOS[$quejaDetalle->tipo_solicitud] ?? ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-primary" style="font-size:0.82rem;padding:6px 10px;">
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}"
                                  style="font-size:0.82rem;padding:6px 10px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>

                        {{-- Involucrados --}}
                        <div class="row mb-3">
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Solicitante</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->user->name }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Correo</small>
                                <span style="font-size:0.82rem;word-break:break-all;">{{ $quejaDetalle->user->email }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Revisor asignado</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->revisor?->name ?? '—' }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Fecha de registro</small>
                                <span style="font-size:0.88rem;">{{ $quejaDetalle->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        {{-- Solicitud --}}
                        <div class="mb-2">
                            <strong style="font-size:0.88rem;">Actividad:</strong>
                            <p class="mb-0" style="font-size:0.9rem;">{{ $quejaDetalle->nombre_actividad }}</p>
                        </div>
                        <div class="mb-3">
                            <strong style="font-size:0.88rem;">Descripción:</strong>
                            <div class="mt-1 p-2 bg-light rounded border"
                                 style="white-space:pre-wrap;font-size:0.88rem;">{{ $quejaDetalle->descripcion }}</div>
                        </div>

                        {{-- Respaldo --}}
                        <div class="mb-3">
                            <strong style="font-size:0.88rem;">Respaldo:</strong>
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
                                <span class="text-muted ml-1" style="font-size:0.88rem;">Sin documento adjunto</span>
                            @endif
                        </div>

                        {{-- Línea de tiempo --}}
                        <hr>
                        <h6 class="mb-2" style="font-size:0.9rem;">
                            <i class="fas fa-stream mr-1"></i>Línea de Tiempo
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
                                <div class="border-left border-info pl-2 flex-grow-1 pb-2">
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
                            <p class="text-muted small">Sin movimientos registrados aún.</p>
                        @endforelse
                    </div>
                    <div class="modal-footer flex-column flex-sm-row align-items-stretch align-items-sm-center">
                        @php $puedeResponder = $quejaDetalle->estado === 'derivado_coordinador'; @endphp
                        @if ($puedeResponder)
                            <button type="button" class="btn btn-success mb-2 mb-sm-0"
                                    wire:click="responderDesdeDetalle({{ $quejaDetalle->id }})">
                                <i class="fas fa-reply mr-1"></i>Responder
                            </button>
                        @endif
                        <button type="button" class="btn btn-secondary" wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal respuesta --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-reply mr-2"></i>Emitir Respuesta
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label>Respuesta / Observación <span class="text-danger">*</span></label>
                            <textarea wire:model="comentario"
                                      class="form-control @error('comentario') is-invalid @enderror"
                                      rows="5"
                                      placeholder="Escriba su respuesta detallada..."></textarea>
                            @error('comentario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" wire:click="responder">
                            <i class="fas fa-check mr-1"></i>Enviar Respuesta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
