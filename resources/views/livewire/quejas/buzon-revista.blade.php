<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    {{-- Indicador de unidad + filtro --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-12 col-md-auto mb-2 mb-md-0">
                    <span class="badge badge-primary px-3 py-1" style="font-size:0.85rem;">
                        <i class="fas fa-book-open mr-1"></i>Unidad: Investigación (Revista)
                    </span>
                </div>
                <div class="col-8 col-md-4">
                    <select wire:model.live="filterEstado" class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 col-md-2 text-right d-flex align-items-center justify-content-end">
                    <span class="badge badge-primary">{{ $quejas->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $queja)
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="d-flex flex-wrap" style="gap:3px;">
                            <span class="text-muted small">#{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:10px;">
                                {{ ucfirst($queja->tipo_solicitud) }}
                            </span>
                        </div>
                        <span class="badge badge-{{ $queja->colorEstado() }}" style="font-size:10px;">
                            {{ $queja->etiquetaEstado() }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <div class="font-weight-bold" style="font-size:0.85rem;line-height:1.3;">
                            {{ Str::limit($queja->nombre_actividad, 50) }}
                        </div>
                        <small class="text-muted">{{ $queja->user->name }} · {{ $queja->created_at->format('d/m/Y') }}</small>
                    </div>
                    <div class="d-flex flex-wrap justify-content-end" style="gap:3px;">
                        <button wire:click="verDetalle({{ $queja->id }})"
                                class="btn btn-xs btn-info" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="{{ route('quejas.reporte-pdf', $queja->id) }}" target="_blank"
                           class="btn btn-xs btn-outline-danger" title="PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @if (!auth()->user()->isSistemas())
                            @if ($esMiembro && !in_array($queja->estado, ['resuelto', 'no_procede']))
                                <button wire:click="abrirResolver({{ $queja->id }})"
                                        class="btn btn-xs btn-purple w-100 mt-1" title="Resolver">
                                    <i class="fas fa-check-double mr-1"></i>Resolver
                                </button>
                            @elseif (!$esMiembro)
                                @if ($queja->estado === 'pendiente')
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'revisar')"
                                            class="btn btn-xs btn-warning" title="Iniciar revisión">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'no_procede')"
                                            class="btn btn-xs btn-danger" title="No Procede">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @elseif (in_array($queja->estado, ['en_revision', 'respondido']))
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'enviar_validacion')"
                                            class="btn btn-xs btn-success" title="Derivar al Jefe">
                                        <i class="fas fa-user-tie"></i>
                                    </button>
                                    <button wire:click="abrirAccion({{ $queja->id }}, 'no_procede')"
                                            class="btn btn-xs btn-danger" title="No Procede">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No hay solicitudes de Investigación / Revista.
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
                        <th>Tipo</th>
                        <th>Actividad</th>
                        <th>Revisor</th>
                        <th class="text-center">Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quejas as $queja)
                        <tr>
                            <td class="font-weight-bold">{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $queja->user->name }}</td>
                            <td>
                                <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                    {{ ucfirst($queja->tipo_solicitud) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($queja->nombre_actividad, 30) }}</td>
                            <td>{{ $queja->revisor?->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $queja->colorEstado() }}"
                                      style="white-space:normal;word-break:break-word;display:inline-block;max-width:120px;">
                                    {{ $queja->etiquetaEstado() }}
                                </span>
                            </td>
                            <td>{{ $queja->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <button wire:click="verDetalle({{ $queja->id }})"
                                        class="btn btn-sm btn-info mr-1" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('quejas.reporte-pdf', $queja->id) }}" target="_blank"
                                   class="btn btn-sm btn-outline-danger mr-1" title="PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if (!auth()->user()->isSistemas())
                                    @if ($esMiembro && !in_array($queja->estado, ['resuelto', 'no_procede']))
                                        <button wire:click="abrirResolver({{ $queja->id }})"
                                                class="btn btn-sm btn-purple" title="Resolver">
                                            <i class="fas fa-check-double mr-1"></i>Resolver
                                        </button>
                                    @elseif (!$esMiembro)
                                        @if ($queja->estado === 'pendiente')
                                            <button wire:click="abrirAccion({{ $queja->id }}, 'revisar')"
                                                    class="btn btn-sm btn-warning mr-1" title="Iniciar revisión">
                                                <i class="fas fa-play"></i> Revisar
                                            </button>
                                            <button wire:click="abrirAccion({{ $queja->id }}, 'no_procede')"
                                                    class="btn btn-sm btn-danger" title="No Procede">
                                                <i class="fas fa-ban"></i> No Procede
                                            </button>
                                        @elseif (in_array($queja->estado, ['en_revision', 'respondido']))
                                            <button wire:click="abrirAccion({{ $queja->id }}, 'enviar_validacion')"
                                                    class="btn btn-sm btn-success mr-1" title="Derivar al Jefe">
                                                <i class="fas fa-share"></i> Derivar al Jefe
                                            </button>
                                            <button wire:click="abrirAccion({{ $queja->id }}, 'no_procede')"
                                                    class="btn btn-sm btn-danger" title="No Procede">
                                                <i class="fas fa-ban"></i> No Procede
                                            </button>
                                        @endif
                                    @endif
                                @endif
                                @if (in_array($queja->estado, ['resuelto', 'no_procede']))
                                    <a href="{{ route('quejas.reporte-final', $queja->id) }}" target="_blank"
                                       class="btn btn-sm btn-danger" title="Reporte Final">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay solicitudes de Investigación / Revista.
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
                            <i class="fas fa-file-alt mr-2"></i>Solicitud #{{ str_pad($quejaDetalle->id, 5, '0', STR_PAD_LEFT) }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-wrap mb-3" style="gap:6px;">
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:0.82rem;padding:6px 10px;">
                                {{ ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-primary" style="font-size:0.82rem;padding:6px 10px;">
                                Investigación (Revista)
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}"
                                  style="font-size:0.82rem;padding:6px 10px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 mb-1">
                                <small class="text-muted d-block">Solicitante</small>
                                <span style="font-size:0.9rem;">{{ $quejaDetalle->user->name }}</span>
                            </div>
                            <div class="col-6 mb-1">
                                <small class="text-muted d-block">Fecha</small>
                                <span style="font-size:0.9rem;">{{ $quejaDetalle->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <strong style="font-size:0.88rem;">Actividad:</strong>
                            <p class="mb-0" style="font-size:0.9rem;">{{ $quejaDetalle->nombre_actividad }}</p>
                        </div>
                        <div class="mb-3">
                            <strong style="font-size:0.88rem;">Descripción:</strong>
                            <p class="mt-1 text-muted border-left border-info pl-3 mb-0"
                               style="font-size:0.9rem;white-space:pre-wrap;">{{ $quejaDetalle->descripcion }}</p>
                        </div>
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
                                <span class="text-muted ml-1" style="font-size:0.88rem;">Sin respaldo adjunto</span>
                            @endif
                        </div>
                        <hr>
                        <h6 class="mb-2" style="font-size:0.9rem;">
                            <i class="fas fa-history mr-1"></i>Historial de seguimiento
                        </h6>
                        @forelse ($quejaDetalle->seguimientos as $seg)
                            <div class="mb-2 p-2 border-left border-primary pl-3"
                                 style="border-left-width:3px !important;">
                                <small class="text-muted d-block">
                                    {{ $seg->created_at->format('d/m/Y H:i') }} · <strong>{{ $seg->user->name }}</strong>
                                </small>
                                <p class="mb-0" style="font-size:0.88rem;">{{ $seg->accion }}</p>
                                @if ($seg->comentario)
                                    <p class="text-muted small mb-0 mt-1">{{ $seg->comentario }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small">Sin movimientos aún.</p>
                        @endforelse
                    </div>
                    <div class="modal-footer flex-column flex-sm-row align-items-stretch align-items-sm-center">
                        <a href="{{ route('quejas.reporte-pdf', $quejaDetalle->id) }}" target="_blank"
                           class="btn btn-outline-danger mb-2 mb-sm-0 mr-sm-auto">
                            <i class="fas fa-file-pdf mr-1"></i>Formulario
                        </a>
                        @if (in_array($quejaDetalle->estado, ['resuelto', 'no_procede']))
                            <a href="{{ route('quejas.reporte-final', $quejaDetalle->id) }}" target="_blank"
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

    {{-- ══════════════════════════════════════════════════════
         Modal Resolver — exclusivo para Miembro Comisión Revista
         ══════════════════════════════════════════════════════ --}}
    @if ($showResolver && $resolverQueja)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.55);">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                 style="max-width:520px;">
                <div class="modal-content">

                    {{-- Cabecera --}}
                    <div class="modal-header" style="background:#6f42c1;color:#fff;">
                        <div>
                            <h5 class="modal-title mb-0" style="font-size:1rem;">
                                <i class="fas fa-check-double mr-2"></i>Resolución de Solicitud
                            </h5>
                            <small style="opacity:.85;">
                                #{{ str_pad($resolverQueja->id, 5, '0', STR_PAD_LEFT) }}
                                &middot; {{ ucfirst($resolverQueja->tipo_solicitud) }}
                                &middot; Investigación (Revista)
                            </small>
                        </div>
                        <button type="button" class="close text-white ml-2" wire:click="cerrarResolver">
                            <span>&times;</span>
                        </button>
                    </div>

                    {{-- Cuerpo --}}
                    <div class="modal-body">

                        {{-- Resumen de la solicitud --}}
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-2 px-3">
                                <div class="row">
                                    <div class="col-12 col-sm-6 mb-1">
                                        <small class="text-muted d-block">Solicitante</small>
                                        <span style="font-size:0.9rem;font-weight:600;">
                                            {{ $resolverQueja->user->name }}
                                        </span>
                                    </div>
                                    <div class="col-12 col-sm-6 mb-1">
                                        <small class="text-muted d-block">Fecha de ingreso</small>
                                        <span style="font-size:0.9rem;">
                                            {{ $resolverQueja->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="col-12 mb-1">
                                        <small class="text-muted d-block">Actividad</small>
                                        <span style="font-size:0.88rem;">{{ $resolverQueja->nombre_actividad }}</span>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block">Estado actual</small>
                                        <span class="badge badge-{{ $resolverQueja->colorEstado() }}"
                                              style="font-size:0.8rem;padding:4px 8px;">
                                            {{ $resolverQueja->etiquetaEstado() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Última respuesta del historial --}}
                        @php
                            $ultimaSeg = $resolverQueja->seguimientos->sortByDesc('created_at')->first();
                        @endphp
                        @if ($ultimaSeg && $ultimaSeg->comentario)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-comment-alt mr-1"></i>Última observación registrada
                                </small>
                                <div class="border-left border-purple pl-3 py-1"
                                     style="border-left-width:3px !important;border-color:#6f42c1 !important;
                                            font-size:0.88rem;color:#444;white-space:pre-wrap;">{{ $ultimaSeg->comentario }}</div>
                            </div>
                        @endif

                        {{-- Observación de resolución --}}
                        <div class="form-group mb-0">
                            <label style="font-size:0.88rem;font-weight:600;">
                                Observación de resolución <span class="text-danger">*</span>
                            </label>
                            <textarea wire:model="resolverComentario"
                                      class="form-control @error('resolverComentario') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Ingrese la observación o fundamento de su decisión..."
                                      style="font-size:0.9rem;resize:vertical;"></textarea>
                            @error('resolverComentario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Pie — dos botones grandes y responsive --}}
                    <div class="modal-footer d-block p-3">
                        <p class="text-center text-muted small mb-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Seleccione la decisión final para esta solicitud
                        </p>
                        <div class="row no-gutters" style="gap:0;">
                            <div class="col-6 pr-1">
                                <button type="button"
                                        wire:click="aceptar"
                                        class="btn btn-success btn-block"
                                        style="font-size:0.9rem;font-weight:700;padding:12px 8px;">
                                    <i class="fas fa-check-circle d-block" style="font-size:1.4rem;margin-bottom:4px;"></i>
                                    ACEPTAR
                                    <small class="d-block font-weight-normal" style="font-size:0.72rem;opacity:.9;">
                                        Marcar como Resuelta
                                    </small>
                                </button>
                            </div>
                            <div class="col-6 pl-1">
                                <button type="button"
                                        wire:click="rechazar"
                                        class="btn btn-danger btn-block"
                                        style="font-size:0.9rem;font-weight:700;padding:12px 8px;">
                                    <i class="fas fa-times-circle d-block" style="font-size:1.4rem;margin-bottom:4px;"></i>
                                    RECHAZAR
                                    <small class="d-block font-weight-normal" style="font-size:0.72rem;opacity:.9;">
                                        Marcar como No Procede
                                    </small>
                                </button>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <button type="button" class="btn btn-sm btn-link text-muted"
                                    wire:click="cerrarResolver">
                                <i class="fas fa-arrow-left mr-1"></i>Cancelar
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- Modal de acción --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-white
                        {{ $accion === 'no_procede' ? 'bg-danger' : ($accion === 'enviar_validacion' ? 'bg-success' : 'bg-primary') }}">
                        <h5 class="modal-title" style="font-size:1rem;">
                            @if ($accion === 'revisar')
                                <i class="fas fa-play mr-2"></i>Iniciar Revisión
                            @elseif ($accion === 'enviar_validacion')
                                <i class="fas fa-share mr-2"></i>Derivar al Jefe de Unidad
                            @elseif ($accion === 'no_procede')
                                <i class="fas fa-ban mr-2"></i>Registrar No Procede
                            @endif
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($accion === 'enviar_validacion')
                            <div class="form-group">
                                <label>Jefe de Unidad <span class="text-danger">*</span></label>
                                <select wire:model="jefeUsuarioId"
                                        class="form-control @error('jefeUsuarioId') is-invalid @enderror">
                                    <option value="0">-- Seleccionar Jefe de Unidad --</option>
                                    @foreach ($jefes as $jefe)
                                        <option value="{{ $jefe->id }}">{{ $jefe->name }}</option>
                                    @endforeach
                                </select>
                                @error('jefeUsuarioId')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        <div class="form-group mb-0">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <textarea wire:model="comentario"
                                      class="form-control @error('comentario') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Ingrese su observación..."></textarea>
                            @error('comentario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button"
                                class="btn {{ $accion === 'no_procede' ? 'btn-danger' : 'btn-primary' }}"
                                wire:click="procesarAccion">
                            <i class="fas fa-check mr-1"></i>Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
