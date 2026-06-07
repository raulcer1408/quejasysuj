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
            <div class="row">
                <div class="col-6 col-md-4 mb-2 mb-md-0">
                    <select wire:model.live="filterEstado" class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <select wire:model.live="filterServicio" class="form-control form-control-sm">
                        <option value="">Todos los servicios</option>
                        @foreach ($servicios as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $queja)
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: ID + tipo + estado --}}
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <span class="text-muted" style="font-size:0.75rem;">#{{ $queja->id }}</span>
                            <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}" style="font-size:10px;">
                                {{ ucfirst($queja->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-{{ $queja->colorEstado() }}" style="font-size:10px;">
                                {{ $queja->etiquetaEstado() }}
                            </span>
                        </div>
                        <small class="text-muted">{{ $queja->created_at->format('d/m/Y') }}</small>
                    </div>
                    {{-- Fila 2: solicitante + servicio --}}
                    <div class="mb-1" style="font-size:0.85rem;">
                        <i class="fas fa-user mr-1 text-muted"></i>
                        <strong>{{ $queja->user->name }}</strong>
                        <span class="text-muted ml-2">· {{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</span>
                    </div>
                    {{-- Fila 3: actividad --}}
                    @if ($queja->nombre_actividad)
                        <div class="text-muted mb-2" style="font-size:0.78rem;">
                            <i class="fas fa-tag mr-1"></i>{{ Str::limit($queja->nombre_actividad, 45) }}
                        </div>
                    @endif
                    {{-- Fila 4: acciones --}}
                    <div class="d-flex flex-wrap" style="gap:4px;">
                        <button wire:click="verDetalle({{ $queja->id }})"
                                class="btn btn-xs btn-info" title="Ver detalle">
                            <i class="fas fa-eye mr-1"></i>Ver
                        </button>
                        <a href="{{ route('quejas.reporte-pdf', $queja->id) }}" target="_blank"
                           class="btn btn-xs btn-outline-danger" title="Formulario PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @if (in_array($queja->estado, ['resuelto', 'no_procede']))
                            <a href="{{ route('quejas.reporte-final', $queja->id) }}" target="_blank"
                               class="btn btn-xs btn-danger" title="Reporte Final PDF">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        @endif
                        @if ($queja->estado === 'derivado_coordinador')
                            <button wire:click="abrirAccion({{ $queja->id }}, 'derivar')"
                                    class="btn btn-xs btn-primary">
                                <i class="fas fa-share mr-1"></i>Derivar
                            </button>
                            <button wire:click="abrirAccion({{ $queja->id }}, 'resolver_derivado')"
                                    class="btn btn-xs btn-success">
                                <i class="fas fa-gavel mr-1"></i>Resolver
                            </button>
                        @elseif ($queja->estado === 'en_validacion')
                            <button wire:click="abrirAccion({{ $queja->id }}, 'derivar')"
                                    class="btn btn-xs btn-primary">
                                <i class="fas fa-share mr-1"></i>Derivar
                            </button>
                            <button wire:click="abrirAccion({{ $queja->id }}, 'resolver')"
                                    class="btn btn-xs btn-success">
                                <i class="fas fa-gavel mr-1"></i>Resolver
                            </button>
                        @elseif ($queja->estado === 'respondido')
                            <button wire:click="abrirAccion({{ $queja->id }}, 'resolver')"
                                    class="btn btn-xs btn-warning">
                                <i class="fas fa-gavel mr-1"></i>Resolver
                            </button>
                        @else
                            <span class="badge badge-success" style="font-size:11px;padding:5px 8px;">
                                <i class="fas fa-check-circle mr-1"></i>Resuelto
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No hay solicitudes pendientes de resolución.
            </div>
        @endforelse
        <div class="mt-2">{{ $quejas->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-primary d-none d-md-block">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-gavel mr-2"></i>Solicitudes para Validar y Resolver</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Revisor</th>
                        <th>Tipo</th>
                        <th>Servicio</th>
                        <th>Actividad</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Resultado</th>
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
                            <td>{{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</td>
                            <td>{{ Str::limit($queja->nombre_actividad, 20) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $queja->colorEstado() }}">
                                    {{ $queja->etiquetaEstado() }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($queja->etiquetaAceptacion())
                                    <span class="badge badge-{{ $queja->colorAceptacion() }}">
                                        {{ $queja->etiquetaAceptacion() }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $queja->created_at->format('d/m/Y') }}</td>
                            <td class="text-center text-nowrap">
                                <button wire:click="verDetalle({{ $queja->id }})"
                                        class="btn btn-sm btn-info mr-1" title="Vista previa">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('quejas.reporte-pdf', $queja->id) }}" target="_blank"
                                   class="btn btn-sm btn-outline-danger mr-1" title="Formulario PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if (in_array($queja->estado, ['resuelto', 'no_procede']))
                                    <a href="{{ route('quejas.reporte-final', $queja->id) }}" target="_blank"
                                       class="btn btn-sm btn-danger mr-1" title="Reporte Final PDF">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                @endif
                                @if ($queja->estado === 'derivado_coordinador')
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'derivar')"
                                            class="btn btn-sm btn-primary mr-1" title="Derivar">
                                        <i class="fas fa-share mr-1"></i>Derivar
                                    </button>
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'resolver_derivado')"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-gavel mr-1"></i>Resolver
                                    </button>
                                @elseif ($queja->estado === 'en_validacion')
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'derivar')"
                                            class="btn btn-sm btn-primary mr-1" title="Derivar">
                                        <i class="fas fa-share mr-1"></i>Derivar
                                    </button>
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'resolver')"
                                            class="btn btn-sm btn-success">
                                        <i class="fas fa-gavel mr-1"></i>Resolver
                                    </button>
                                @elseif ($queja->estado === 'respondido')
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'resolver')"
                                            class="btn btn-sm btn-warning">
                                        <i class="fas fa-gavel mr-1"></i>Resolver
                                    </button>
                                @else
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i>Resuelto
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay solicitudes pendientes de resolución.
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
                            <i class="fas fa-file-alt mr-2"></i>Detalle #{{ $quejaDetalle->id }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{-- Badges de estado --}}
                        <div class="d-flex flex-wrap mb-3" style="gap:6px;">
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                {{ ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-primary">
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Solicitante</small>
                                <div class="font-weight-bold">{{ $quejaDetalle->user->name }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Fecha</small>
                                <div>{{ $quejaDetalle->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Revisor</small>
                                <div>{{ $quejaDetalle->revisor?->name ?? '—' }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Derivado a</small>
                                <div>{{ $quejaDetalle->coordinador?->name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Actividad</small>
                            <div>{{ $quejaDetalle->nombre_actividad }}</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Descripción</small>
                            <p class="mt-1 text-muted border-left border-info pl-3 mb-0">{{ $quejaDetalle->descripcion }}</p>
                        </div>
                        @if ($quejaDetalle->respaldo)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Respaldo adjunto</small>
                                <a href="{{ Storage::url($quejaDetalle->respaldo) }}" target="_blank"
                                   class="btn btn-sm btn-outline-danger mr-1">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </a>
                                <a href="{{ Storage::url($quejaDetalle->respaldo) }}" download
                                   class="btn btn-sm btn-danger">
                                    <i class="fas fa-download mr-1"></i>Descargar
                                </a>
                            </div>
                        @endif
                        <hr>
                        <h6><i class="fas fa-history mr-1"></i>Historial de seguimiento</h6>
                        @forelse ($quejaDetalle->seguimientos as $seg)
                            <div class="mb-2 p-2 border-left border-primary pl-3">
                                <small class="text-muted">
                                    {{ $seg->created_at->format('d/m/Y H:i') }} —
                                    <strong>{{ $seg->user->name }}</strong>
                                </small>
                                <p class="mb-0">{{ $seg->accion }}</p>
                                @if ($seg->comentario)
                                    <p class="text-muted small mb-0">{{ $seg->comentario }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small">Sin movimientos aún.</p>
                        @endforelse
                    </div>
                    <div class="modal-footer flex-column flex-sm-row align-items-stretch align-items-sm-center">
                        <a href="{{ route('quejas.reporte-pdf', $quejaDetalle->id) }}" target="_blank"
                           class="btn btn-outline-danger btn-sm mb-2 mb-sm-0 mr-sm-auto">
                            <i class="fas fa-file-pdf mr-1"></i>Formulario
                        </a>
                        @if (in_array($quejaDetalle->estado, ['resuelto', 'no_procede']))
                            <a href="{{ route('quejas.reporte-final', $quejaDetalle->id) }}" target="_blank"
                               class="btn btn-danger btn-sm mb-2 mb-sm-0">
                                <i class="fas fa-file-alt mr-1"></i>Reporte Final
                            </a>
                        @endif
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de acción (Resolver / Validar / Derivar) --}}
    @if ($showModal && $quejaDetalle)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header {{ $accion === 'derivar' ? 'bg-primary' : 'bg-success' }} text-white py-2">
                        <h5 class="modal-title" style="font-size:0.95rem;">
                            @if ($accion === 'validar')
                                <i class="fas fa-gavel mr-1"></i>Resolver
                            @elseif ($accion === 'derivar')
                                <i class="fas fa-share mr-1"></i>Derivar
                            @else
                                <i class="fas fa-gavel mr-1"></i>Resolver
                            @endif
                            — <span class="font-weight-bold">#{{ str_pad($quejaDetalle->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }} ml-1" style="font-size:0.75rem;">
                                {{ \App\Models\Queja::TIPOS[$quejaDetalle->tipo_solicitud] ?? ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-2 p-md-3">

                        {{-- ══════════════════════════════════════════════
                             MÓVIL: formulario primero, detalle colapsable
                             ══════════════════════════════════════════════ --}}
                        <div class="d-md-none">

                            {{-- Resumen compacto --}}
                            <div class="d-flex flex-wrap align-items-center mb-2" style="gap:5px;">
                                <span class="badge badge-primary" style="font-size:11px;">
                                    {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                                </span>
                                <span class="badge badge-{{ $quejaDetalle->colorEstado() }}" style="font-size:11px;">
                                    {{ $quejaDetalle->etiquetaEstado() }}
                                </span>
                                <span class="text-muted" style="font-size:0.75rem;">
                                    {{ $quejaDetalle->user->name }}
                                </span>
                            </div>

                            {{-- Formulario de acción — PRIMERO en móvil --}}
                            <div class="card mb-3 shadow-sm border-left border-{{ $accion === 'derivar' ? 'primary' : 'success' }}" style="border-left-width:4px!important;">
                                <div class="card-body py-3 px-3">
                                    <h6 class="font-weight-bold mb-3" style="font-size:0.9rem;color:{{ $accion === 'derivar' ? '#007bff' : '#28a745' }};">
                                        @if ($accion === 'validar')
                                            <i class="fas fa-check mr-1"></i>Observación de Validación
                                        @elseif ($accion === 'derivar')
                                            <i class="fas fa-share mr-1"></i>Derivar a
                                        @else
                                            <i class="fas fa-gavel mr-1"></i>Respuesta a la Solicitud
                                        @endif
                                    </h6>
                                    @if ($accion === 'derivar')
                                        <div class="form-group">
                                            <label class="small font-weight-bold">Destinatario <span class="text-danger">*</span></label>
                                            <select wire:model="derivarUsuarioId"
                                                    class="form-control @error('derivarUsuarioId') is-invalid @enderror">
                                                <option value="0">-- Seleccionar --</option>
                                                @foreach ($usuariosDerivacion as $u)
                                                    <option value="{{ $u->id }}">
                                                        {{ $u->name }} — {{ \App\Models\User::roles()[$u->role] ?? $u->role }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('derivarUsuarioId')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">
                                            {{ $accion === 'validar' ? 'Observación' : ($accion === 'derivar' ? 'Justificación' : 'Resolución') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea wire:model="comentario"
                                                  class="form-control @error('comentario') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="{{ $accion === 'validar' ? 'Escriba su observación...' : ($accion === 'derivar' ? 'Justificación de la derivación...' : 'Escriba la resolución...') }}"></textarea>
                                        @error('comentario')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Detalle colapsable --}}
                            <details class="mb-2">
                                <summary class="small font-weight-bold text-muted mb-2" style="cursor:pointer;list-style:none;outline:none;">
                                    <i class="fas fa-chevron-right mr-1" style="font-size:0.7rem;"></i>Ver datos completos de la solicitud
                                </summary>
                                <div class="mt-2">
                                    {{-- Solicitante --}}
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-body py-2 px-3">
                                            <div class="small font-weight-bold text-muted mb-2" style="font-size:0.78rem;text-transform:uppercase;">
                                                <i class="fas fa-user mr-1"></i>Datos del Solicitante
                                            </div>
                                            <div class="row" style="font-size:0.83rem;">
                                                <div class="col-6 mb-1">
                                                    <div class="text-muted" style="font-size:0.72rem;">Nombre</div>
                                                    <div>{{ $quejaDetalle->user->name }}</div>
                                                </div>
                                                <div class="col-6 mb-1">
                                                    <div class="text-muted" style="font-size:0.72rem;">Revisor</div>
                                                    <div>{{ $quejaDetalle->revisor?->name ?? '—' }}</div>
                                                </div>
                                                <div class="col-6 mb-1">
                                                    <div class="text-muted" style="font-size:0.72rem;">Coordinador</div>
                                                    <div>{{ $quejaDetalle->coordinador?->name ?? '—' }}</div>
                                                </div>
                                                <div class="col-6 mb-1">
                                                    <div class="text-muted" style="font-size:0.72rem;">Fecha</div>
                                                    <div>{{ $quejaDetalle->created_at->format('d/m/Y H:i') }}</div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="text-muted" style="font-size:0.72rem;">Actividad</div>
                                                    <div>{{ $quejaDetalle->nombre_actividad }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Descripción --}}
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-body py-2 px-3">
                                            <div class="small font-weight-bold text-muted mb-1" style="font-size:0.78rem;text-transform:uppercase;">
                                                <i class="fas fa-align-left mr-1"></i>Descripción
                                            </div>
                                            <div class="p-2 bg-light rounded border" style="white-space:pre-wrap;font-size:0.83rem;">{{ $quejaDetalle->descripcion }}</div>
                                        </div>
                                    </div>

                                    {{-- Respaldo --}}
                                    @if ($quejaDetalle->respaldo)
                                        <div class="mb-2 d-flex" style="gap:6px;">
                                            <a href="{{ Storage::url($quejaDetalle->respaldo) }}" target="_blank"
                                               class="btn btn-sm btn-outline-danger flex-fill">
                                                <i class="fas fa-eye mr-1"></i>Ver respaldo
                                            </a>
                                            <a href="{{ Storage::url($quejaDetalle->respaldo) }}" download
                                               class="btn btn-sm btn-danger flex-fill">
                                                <i class="fas fa-download mr-1"></i>Descargar
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Historial --}}
                                    <div class="card shadow-sm mb-1">
                                        <div class="card-body py-2 px-3">
                                            <div class="small font-weight-bold text-muted mb-2" style="font-size:0.78rem;text-transform:uppercase;">
                                                <i class="fas fa-stream mr-1"></i>Historial del flujo
                                            </div>
                                            @forelse ($quejaDetalle->seguimientos->sortBy('created_at') as $seg)
                                                <div class="d-flex mb-2">
                                                    <div class="mr-2 text-center" style="min-width:38px;">
                                                        <small class="text-muted d-block" style="font-size:0.65rem;line-height:1.2;">{{ $seg->created_at->format('d/m') }}</small>
                                                        <small class="text-muted d-block" style="font-size:0.65rem;line-height:1.2;">{{ $seg->created_at->format('H:i') }}</small>
                                                    </div>
                                                    <div class="border-left border-info pl-2 flex-grow-1 pb-1">
                                                        <div class="d-flex flex-wrap align-items-center mb-1" style="gap:3px;">
                                                            <span class="badge badge-{{ \App\Models\Queja::COLORES_ESTADO[$seg->estado_nuevo] ?? 'secondary' }}" style="font-size:9px;">
                                                                {{ \App\Models\Queja::ESTADOS[$seg->estado_nuevo] ?? $seg->estado_nuevo }}
                                                            </span>
                                                            <small class="text-muted" style="font-size:0.72rem;">{{ $seg->user->name }}</small>
                                                        </div>
                                                        <p class="mb-0" style="font-size:0.78rem;">{{ $seg->accion }}</p>
                                                        @if ($seg->comentario)
                                                            <p class="text-muted mb-0 mt-1 border-left border-secondary pl-2" style="font-size:0.75rem;">{{ $seg->comentario }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted small mb-0">Sin movimientos aún.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </details>

                        </div>{{-- fin d-md-none --}}

                        {{-- ══════════════════════════════════════════
                             ESCRITORIO: layout original (detalle + form)
                             ══════════════════════════════════════════ --}}
                        <div class="d-none d-md-block">

                            {{-- Info resumida --}}
                            <div class="d-flex flex-wrap mb-3" style="gap:6px;">
                                <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                    {{ \App\Models\Queja::TIPOS[$quejaDetalle->tipo_solicitud] ?? ucfirst($quejaDetalle->tipo_solicitud) }}
                                </span>
                                <span class="badge badge-primary">
                                    {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                                </span>
                                <span class="badge badge-{{ $quejaDetalle->colorEstado() }}">
                                    {{ $quejaDetalle->etiquetaEstado() }}
                                </span>
                            </div>

                            {{-- Datos del solicitante --}}
                            <div class="card card-outline card-secondary mb-3">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0" style="font-size:0.85rem;">
                                        <i class="fas fa-user mr-1"></i>Datos del Solicitante
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-6 col-md-4 mb-1">
                                            <small class="text-muted d-block">Nombre</small>
                                            {{ $quejaDetalle->user->name }}
                                        </div>
                                        <div class="col-6 col-md-4 mb-1">
                                            <small class="text-muted d-block">Revisor</small>
                                            {{ $quejaDetalle->revisor?->name ?? '—' }}
                                        </div>
                                        <div class="col-6 col-md-4 mb-1">
                                            <small class="text-muted d-block">Coordinador</small>
                                            {{ $quejaDetalle->coordinador?->name ?? '—' }}
                                        </div>
                                        <div class="col-6 col-md-6 mb-1">
                                            <small class="text-muted d-block">Actividad</small>
                                            {{ $quejaDetalle->nombre_actividad }}
                                        </div>
                                        <div class="col-6 col-md-6 mb-1">
                                            <small class="text-muted d-block">Fecha</small>
                                            {{ $quejaDetalle->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <div class="card card-outline card-primary mb-3">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0" style="font-size:0.85rem;">
                                        <i class="fas fa-align-left mr-1"></i>Descripción
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="p-2 bg-light rounded border" style="white-space:pre-wrap;font-size:0.88rem;">{{ $quejaDetalle->descripcion }}</div>
                                </div>
                            </div>

                            {{-- Respaldo --}}
                            @if ($quejaDetalle->respaldo)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Respaldo adjunto</small>
                                    <a href="{{ Storage::url($quejaDetalle->respaldo) }}" target="_blank"
                                       class="btn btn-sm btn-outline-danger mr-1">
                                        <i class="fas fa-eye mr-1"></i>Ver
                                    </a>
                                    <a href="{{ Storage::url($quejaDetalle->respaldo) }}" download
                                       class="btn btn-sm btn-danger">
                                        <i class="fas fa-download mr-1"></i>Descargar
                                    </a>
                                </div>
                            @endif

                            {{-- Historial --}}
                            <div class="card card-outline card-info mb-3">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0" style="font-size:0.85rem;">
                                        <i class="fas fa-stream mr-1"></i>Historial del flujo
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    @forelse ($quejaDetalle->seguimientos->sortBy('created_at') as $seg)
                                        <div class="d-flex mb-3">
                                            <div class="mr-3 text-center" style="min-width:50px;">
                                                <small class="text-muted d-block" style="font-size:0.7rem;">{{ $seg->created_at->format('d/m') }}</small>
                                                <small class="text-muted d-block" style="font-size:0.7rem;">{{ $seg->created_at->format('H:i') }}</small>
                                            </div>
                                            <div class="border-left border-info pl-3 flex-grow-1 pb-2">
                                                <div class="d-flex flex-wrap align-items-center mb-1" style="gap:4px;">
                                                    <span class="badge badge-{{ \App\Models\Queja::COLORES_ESTADO[$seg->estado_nuevo] ?? 'secondary' }}" style="font-size:10px;">
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
                                        <p class="text-muted small mb-0">Sin movimientos registrados aún.</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Formulario de acción --}}
                            <div class="card card-outline {{ $accion === 'derivar' ? 'card-primary' : 'card-success' }} mb-0">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0" style="font-size:0.85rem;">
                                        @if ($accion === 'validar')
                                            <i class="fas fa-check mr-1"></i>Observación de Validación
                                        @elseif ($accion === 'derivar')
                                            <i class="fas fa-share mr-1"></i>Derivar a
                                        @else
                                            <i class="fas fa-gavel mr-1"></i>Respuesta a la Solicitud
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    @if ($accion === 'derivar')
                                        <div class="form-group">
                                            <label class="small font-weight-bold">Seleccionar destinatario <span class="text-danger">*</span></label>
                                            <select wire:model="derivarUsuarioId"
                                                    class="form-control @error('derivarUsuarioId') is-invalid @enderror">
                                                <option value="0">-- Seleccionar usuario --</option>
                                                @foreach ($usuariosDerivacion as $u)
                                                    <option value="{{ $u->id }}">
                                                        {{ $u->name }} — {{ \App\Models\User::roles()[$u->role] ?? $u->role }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('derivarUsuarioId')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">
                                            {{ $accion === 'validar' ? 'Observación' : ($accion === 'derivar' ? 'Justificación' : 'Resolución') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea wire:model="comentario"
                                                  class="form-control @error('comentario') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="{{ $accion === 'validar' ? 'Escriba su observación de validación...' : ($accion === 'derivar' ? 'Justificación de la derivación...' : 'Escriba la resolución de la solicitud...') }}"></textarea>
                                        @error('comentario')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>{{-- fin d-none d-md-block --}}

                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer py-2">
                        <div class="d-flex flex-column flex-md-row w-100 w-md-auto justify-content-md-end" style="gap:6px;">
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="cerrarModal">
                                <i class="fas fa-times mr-1"></i>Cancelar
                            </button>
                            @if ($accion === 'validar')
                                <button type="button" class="btn btn-success btn-sm" wire:click="procesarAccion">
                                    <i class="fas fa-gavel mr-1"></i>Confirmar Resolución
                                </button>
                            @elseif ($accion === 'derivar')
                                <button type="button" class="btn btn-primary btn-sm" wire:click="procesarAccion">
                                    <i class="fas fa-share mr-1"></i>Confirmar Derivación
                                </button>
                            @else
                                <button type="button" class="btn btn-success btn-sm" wire:click="aceptar">
                                    <i class="fas fa-check-circle mr-1"></i>Aceptar solicitud
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="rechazar">
                                    <i class="fas fa-times-circle mr-1"></i>Rechazar (No Procede)
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
