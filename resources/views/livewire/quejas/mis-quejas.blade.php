<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtro --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <select wire:model.live="filterEstado" class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                @if (auth()->user()->isVisitante() || auth()->user()->isSuperusuario() || auth()->user()->isSistemas())
                <div class="col-12 col-md-4">
                    <a href="{{ route('quejas.nueva') }}" class="btn btn-primary btn-sm btn-block d-md-inline-block" style="max-width:180px;">
                        <i class="fas fa-plus mr-1"></i>Nueva Solicitud
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $item)
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <span class="text-muted small">#{{ $item->id }}</span>
                            <span class="badge {{ $item->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }} ml-1">
                                {{ ucfirst($item->tipo_solicitud) }}
                            </span>
                        </div>
                        <span class="badge badge-{{ $item->colorEstado() }}">
                            {{ $item->etiquetaEstado() }}
                        </span>
                    </div>
                    <p class="mb-1 font-weight-bold" style="font-size:0.88rem;line-height:1.3;">
                        {{ Str::limit($item->nombre_actividad, 50) }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ \App\Models\Queja::SERVICIOS[$item->servicio] ?? $item->servicio }}
                            &nbsp;·&nbsp;{{ $item->created_at->format('d/m/Y') }}
                        </small>
                        <div>
                            @if ($item->coordinador_id === auth()->id() && $item->estado === 'derivado_coordinador')
                                <button wire:click="abrirRespuesta({{ $item->id }})"
                                        class="btn btn-sm btn-warning mr-1" title="Responder">
                                    <i class="fas fa-reply"></i>
                                </button>
                            @endif
                            <button wire:click="verDetalle({{ $item->id }})"
                                    class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                No tienes solicitudes registradas.
            </div>
        @endforelse

        <div class="mt-2">{{ $quejas->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-primary d-none d-md-block">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list-alt mr-2"></i>Mis Solicitudes</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>Servicio</th>
                        <th>Actividad</th>
                        <th class="text-center">Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quejas as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <span class="badge {{ $item->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                    {{ ucfirst($item->tipo_solicitud) }}
                                </span>
                            </td>
                            <td>{{ \App\Models\Queja::SERVICIOS[$item->servicio] ?? $item->servicio }}</td>
                            <td>{{ Str::limit($item->nombre_actividad, 30) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item->colorEstado() }}">
                                    {{ $item->etiquetaEstado() }}
                                </span>
                            </td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if ($item->coordinador_id === auth()->id() && $item->estado === 'derivado_coordinador')
                                    <button wire:click="abrirRespuesta({{ $item->id }})"
                                            class="btn btn-sm btn-warning mr-1" title="Responder">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                @endif
                                <button wire:click="verDetalle({{ $item->id }})"
                                        class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No tienes solicitudes registradas.
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

                        {{-- Info básica en chips --}}
                        <div class="d-flex flex-wrap gap-2 mb-3" style="gap:8px;">
                            <span class="badge badge-lg {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}" style="font-size:0.82rem;padding:6px 10px;">
                                {{ ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                            <span class="badge badge-lg badge-secondary" style="font-size:0.82rem;padding:6px 10px;">
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </span>
                            <span class="badge badge-lg badge-{{ $quejaDetalle->colorEstado() }}" style="font-size:0.82rem;padding:6px 10px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>Actividad:</strong>
                            <p class="mb-0 mt-1" style="font-size:0.93rem;">{{ $quejaDetalle->nombre_actividad }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Descripción:</strong>
                            <p class="mt-1 text-muted mb-0" style="font-size:0.9rem;white-space:pre-wrap;">{{ $quejaDetalle->descripcion }}</p>
                        </div>

                        {{-- Respaldo --}}
                        <div class="mb-3">
                            <strong>Respaldo adjunto:</strong>
                            @if ($quejaDetalle->respaldo)
                                <div class="mt-2">
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
                                <span class="text-muted ml-2">Sin respaldo adjunto</span>
                            @endif
                        </div>

                        {{-- Resolución final --}}
                        @if (in_array($quejaDetalle->estado, ['resuelto', 'no_procede']))
                            @php
                                $segCierre = $quejaDetalle->seguimientos
                                    ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                                    ->sortByDesc('created_at')->first();
                            @endphp
                            <hr>
                            <div class="alert {{ $quejaDetalle->estado === 'resuelto' ? 'alert-success' : 'alert-danger' }} mb-3">
                                <h6 class="font-weight-bold mb-2" style="font-size:0.9rem;">
                                    <i class="fas {{ $quejaDetalle->estado === 'resuelto' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                    Resolución Final — {{ $quejaDetalle->estado === 'resuelto' ? 'ACEPTADA' : 'NO PROCEDE' }}
                                </h6>
                                @if ($segCierre)
                                    <p class="mb-1 small">
                                        <strong>Por:</strong> {{ $segCierre->user->name }}
                                        &nbsp;·&nbsp;
                                        {{ $segCierre->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    @if ($segCierre->comentario)
                                        <div class="mt-2 p-2 bg-white rounded border" style="font-size:13px;">
                                            {{ $segCierre->comentario }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif

                        {{-- Historial --}}
                        <hr>
                        <h6 class="mb-2"><i class="fas fa-history mr-1"></i>Historial de seguimiento</h6>
                        @forelse ($quejaDetalle->seguimientos as $seg)
                            <div class="mb-2 p-2 border-left border-primary pl-3" style="border-left-width:3px !important;">
                                <small class="text-muted d-block">
                                    {{ $seg->created_at->format('d/m/Y H:i') }}
                                    &nbsp;·&nbsp;<strong>{{ $seg->user->name }}</strong>
                                </small>
                                <p class="mb-0 mt-1" style="font-size:0.88rem;">{{ $seg->accion }}</p>
                                @if ($seg->comentario)
                                    <p class="text-muted small mb-0 mt-1">{{ $seg->comentario }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small">Sin movimientos aún.</p>
                        @endforelse

                    </div>
                    <div class="modal-footer flex-column flex-sm-row">
                        <button type="button" class="btn btn-secondary btn-block d-sm-inline-block" style="max-width:100%;"
                                wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal respuesta (para docentes asignados como coordinador) --}}
    @if ($showModal && $quejaResponder)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-reply mr-2"></i>Responder Solicitud #{{ $quejaResponder->id }}
                        </h5>
                        <button type="button" class="close" wire:click="cerrarModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2 small text-muted">
                            <strong>Actividad:</strong> {{ $quejaResponder->nombre_actividad }}
                        </p>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold" style="font-size:0.9rem;">
                                Respuesta <span class="text-danger">*</span>
                            </label>
                            <textarea wire:model="comentario"
                                      class="form-control form-control-sm @error('comentario') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Escriba su respuesta..."></textarea>
                            @error('comentario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="cerrarModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" wire:click="responder">
                            <i class="fas fa-paper-plane mr-1"></i>Enviar Respuesta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
