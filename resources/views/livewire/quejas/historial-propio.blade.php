<div>
    @php
        $puedeVerPdf = auth()->user()->isSuperusuario() || auth()->user()->isSistemas()
                    || auth()->user()->isRevisor() || auth()->user()->isJefeUnidad();
    @endphp
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
            <i class="fas fa-history mr-1"></i>
            @php $u = Auth::user(); @endphp
            @if ($u->isJefeUnidad())
                Solicitudes de Mi Unidad
            @elseif ($u->isRevisor())
                Solicitudes Revisadas
            @elseif ($u->isCoordinador())
                Solicitudes Asignadas
            @elseif ($u->isSuperusuario() || $u->isSistemas())
                Registro Histórico General
            @else
                Mis Solicitudes
            @endif
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
                    {{-- Fila 2: actividad + solicitante·servicio --}}
                    <div class="mb-1">
                        <div class="font-weight-bold" style="font-size:0.85rem;line-height:1.3;">
                            {{ Str::limit($item->nombre_actividad, 50) }}
                        </div>
                        <small class="text-muted">
                            {{ $item->user->name }} ·
                            {{ \App\Models\Queja::SERVICIOS[$item->servicio] ?? $item->servicio }}
                        </small>
                    </div>
                    {{-- Fila 3: actores involucrados + fecha + acción --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size:0.73rem;color:#888;line-height:1.5;">
                            @if ($item->revisor)
                                <div><i class="fas fa-search mr-1"></i>{{ $item->revisor->name }}</div>
                            @endif
                            @if ($item->coordinador)
                                <div><i class="fas fa-share mr-1"></i>{{ $item->coordinador->name }}</div>
                            @endif
                            @if ($item->jefe)
                                <div><i class="fas fa-gavel mr-1"></i>{{ $item->jefe->name }}</div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center flex-shrink-0 ml-2 flex-wrap justify-content-end" style="gap:4px;">
                            <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                            <button wire:click="verDetalle({{ $item->id }})"
                                    class="btn btn-xs btn-info" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if ($puedeVerPdf)
                                <a href="{{ route('quejas.reporte-pdf', $item->id) }}" target="_blank"
                                   class="btn btn-xs btn-outline-danger" title="Formulario PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if (in_array($item->estado, ['resuelto', 'no_procede']))
                                    <a href="{{ route('quejas.reporte-final', $item->id) }}" target="_blank"
                                       class="btn btn-xs btn-danger" title="Reporte Final PDF">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                @endif
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
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>Registro Histórico
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $quejas->total() }} solicitudes</span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0" style="font-size:0.82rem;">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Servicio</th>
                        <th>Actividad</th>
                        <th class="text-center">Estado</th>
                        <th>Revisor</th>
                        <th>Coordinador</th>
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
                            <td>{{ Str::limit($item->nombre_actividad, 25) }}</td>
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
                                @if ($puedeVerPdf)
                                    <a href="{{ route('quejas.reporte-pdf', $item->id) }}" target="_blank"
                                       class="btn btn-sm btn-outline-danger mr-1" title="Formulario PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @if (in_array($item->estado, ['resuelto', 'no_procede']))
                                        <a href="{{ route('quejas.reporte-final', $item->id) }}" target="_blank"
                                           class="btn btn-sm btn-danger" title="Reporte Final PDF">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    @endif
                                @endif
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
                    <div class="modal-header bg-info text-white py-2">
                        <h5 class="modal-title" style="font-size:0.95rem;">
                            <i class="fas fa-file-alt mr-2"></i>
                            Solicitud #{{ $quejaDetalle->id }}
                            <span class="badge {{ $quejaDetalle->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }} ml-1"
                                  style="font-size:0.75rem;">
                                {{ ucfirst($quejaDetalle->tipo_solicitud) }}
                            </span>
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarDetalle">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-2 p-md-3">

                        {{-- Badges --}}
                        <div class="d-flex flex-wrap mb-3" style="gap:5px;">
                            <span class="badge badge-secondary" style="font-size:0.8rem;padding:5px 8px;">
                                {{ \App\Models\Queja::SERVICIOS[$quejaDetalle->servicio] ?? $quejaDetalle->servicio }}
                            </span>
                            <span class="badge badge-{{ $quejaDetalle->colorEstado() }}" style="font-size:0.8rem;padding:5px 8px;">
                                {{ $quejaDetalle->etiquetaEstado() }}
                            </span>
                        </div>

                        {{-- Actores --}}
                        <div class="row mb-2" style="font-size:0.85rem;">
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Solicitante</small>
                                {{ $quejaDetalle->user->name }}
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Fecha</small>
                                {{ $quejaDetalle->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Revisor</small>
                                {{ $quejaDetalle->revisor?->name ?? '—' }}
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Coordinador</small>
                                {{ $quejaDetalle->coordinador?->name ?? '—' }}
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
                            <p class="mt-0 border-left border-info pl-3 mb-0"
                               style="font-size:0.88rem;white-space:pre-wrap;color:#444;">{{ $quejaDetalle->descripcion }}</p>
                        </div>

                        @if ($quejaDetalle->respaldo)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Respaldo adjunto</small>
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
                            </div>
                        @endif

                        <hr class="my-2">
                        <h6 class="mb-2" style="font-size:0.88rem;">
                            <i class="fas fa-history mr-1"></i>Historial de seguimiento
                        </h6>
                        @forelse ($quejaDetalle->seguimientos->sortBy('created_at') as $seg)
                            <div class="d-flex mb-2">
                                <div class="mr-2 text-center" style="min-width:38px;">
                                    <small class="text-muted d-block" style="font-size:0.68rem;line-height:1.3;">{{ $seg->created_at->format('d/m') }}</small>
                                    <small class="text-muted d-block" style="font-size:0.68rem;line-height:1.3;">{{ $seg->created_at->format('H:i') }}</small>
                                </div>
                                <div class="border-left border-primary pl-2 flex-grow-1 pb-2">
                                    <div class="d-flex flex-wrap align-items-center mb-1" style="gap:4px;">
                                        <span class="badge badge-{{ \App\Models\Queja::COLORES_ESTADO[$seg->estado_nuevo] ?? 'secondary' }}"
                                              style="font-size:10px;">
                                            {{ \App\Models\Queja::ESTADOS[$seg->estado_nuevo] ?? $seg->estado_nuevo }}
                                        </span>
                                        <small class="text-muted" style="font-size:0.75rem;">
                                            por <strong>{{ $seg->user->name }}</strong>
                                        </small>
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
                    <div class="modal-footer py-2 flex-column flex-sm-row align-items-stretch align-items-sm-center">
                        @if ($puedeVerPdf)
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
                        @endif
                        <button type="button" class="btn btn-secondary btn-sm w-100 w-sm-auto"
                                wire:click="cerrarDetalle">
                            <i class="fas fa-times mr-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
