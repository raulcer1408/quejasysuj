<div>
    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-header py-2">
            <h6 class="card-title mb-0"><i class="fas fa-filter mr-1"></i>Filtros</h6>
        </div>
        <div class="card-body py-2">
            <div class="row align-items-end">
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Estado</label>
                    <select wire:model.live="filterEstado" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
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
                            @foreach ($servicios as $valor => $etiqueta)
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Tipo</label>
                    <select wire:model.live="filterTipo" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach ($tipos as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Departamento</label>
                    <select wire:model.live="filterDepartamento" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach ($departamentos as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Desde</label>
                    <input wire:model.live="fechaDesde" type="date" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Hasta</label>
                    <input wire:model.live="fechaHasta" type="date" class="form-control form-control-sm">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12 text-right">
                    <button wire:click="$set('filterEstado',''); $set('filterServicio',''); $set('filterTipo',''); $set('filterDepartamento',''); $set('fechaDesde',''); $set('fechaHasta','')"
                            class="btn btn-sm btn-secondary">
                        <i class="fas fa-times mr-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cabecera + exportar --}}
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:8px;">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-clipboard-list mr-1"></i>
            Solicitudes encontradas:
            <span class="badge badge-danger ml-1">{{ $total }}</span>
        </h6>
        <div>
            <button wire:click="exportExcel" class="btn btn-sm btn-success mr-1" title="Exportar a Excel">
                <i class="fas fa-file-excel mr-1"></i>
                <span class="d-none d-sm-inline">Excel</span>
            </button>
            <button wire:click="exportPdf" class="btn btn-sm btn-danger" title="Exportar a PDF">
                <i class="fas fa-file-pdf mr-1"></i>
                <span class="d-none d-sm-inline">PDF</span>
            </button>
        </div>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($quejas as $queja)
            @php
                $segCierre = $queja->seguimientos
                    ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                    ->sortByDesc('created_at')->first();
                $dias = $segCierre
                    ? (int) $queja->created_at->diffInDays($segCierre->created_at)
                    : null;
            @endphp
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: ID + tipo + estado + días --}}
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="d-flex flex-wrap" style="gap:3px;">
                            <span class="text-muted small font-weight-bold">
                                #{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}"
                                  style="font-size:10px;">
                                {{ \App\Models\Queja::TIPOS[$queja->tipo_solicitud] ?? $queja->tipo_solicitud }}
                            </span>
                            @if ($dias !== null)
                                <span class="badge {{ $dias <= 3 ? 'badge-success' : ($dias <= 10 ? 'badge-warning' : 'badge-danger') }}"
                                      style="font-size:10px;">
                                    {{ $dias }}d
                                </span>
                            @endif
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
                            {{ $queja->user->name }} ·
                            {{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}
                        </small>
                    </div>
                    {{-- Fila 3: departamento + revisor + fecha --}}
                    <div class="d-flex flex-wrap justify-content-between" style="gap:4px;">
                        <div>
                            @if ($queja->user->departamento)
                                <span class="badge badge-light border" style="font-size:10px;color:#555;">
                                    {{ \App\Models\User::DEPARTAMENTOS[$queja->user->departamento] ?? $queja->user->departamento }}
                                </span>
                            @endif
                            @if ($queja->revisor)
                                <span class="text-muted" style="font-size:0.75rem;">
                                    <i class="fas fa-user-check mr-1"></i>{{ $queja->revisor->name }}
                                </span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $queja->created_at->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No se encontraron solicitudes con los filtros aplicados.
            </div>
        @endforelse
        <div class="mt-2">{{ $quejas->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-danger d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" style="font-size:0.875rem;">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Servicio</th>
                            <th>Actividad</th>
                            <th>Solicitante</th>
                            <th>Departamento</th>
                            <th class="text-center">Estado</th>
                            <th>Revisor</th>
                            <th>Jefe</th>
                            <th class="text-center">Registro</th>
                            <th class="text-center">Días</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quejas as $queja)
                            @php
                                $segCierre = $queja->seguimientos
                                    ->whereIn('estado_nuevo', ['resuelto', 'no_procede'])
                                    ->sortByDesc('created_at')->first();
                                $dias = $segCierre
                                    ? (int) $queja->created_at->diffInDays($segCierre->created_at)
                                    : null;
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ str_pad($queja->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <span class="badge {{ $queja->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}">
                                        {{ \App\Models\Queja::TIPOS[$queja->tipo_solicitud] ?? $queja->tipo_solicitud }}
                                    </span>
                                </td>
                                <td>{{ \App\Models\Queja::SERVICIOS[$queja->servicio] ?? $queja->servicio }}</td>
                                <td>{{ Str::limit($queja->nombre_actividad, 25) }}</td>
                                <td>{{ $queja->user->name }}</td>
                                <td>{{ \App\Models\User::DEPARTAMENTOS[$queja->user->departamento ?? ''] ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $queja->colorEstado() }}"
                                          style="white-space:normal;word-break:break-word;display:inline-block;max-width:100px;">
                                        {{ $queja->etiquetaEstado() }}
                                    </span>
                                </td>
                                <td>{{ $queja->revisor?->name ?? '—' }}</td>
                                <td>{{ $queja->jefe?->name ?? '—' }}</td>
                                <td class="text-center text-muted">{{ $queja->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    @if ($dias !== null)
                                        <span class="badge {{ $dias <= 3 ? 'badge-success' : ($dias <= 10 ? 'badge-warning' : 'badge-danger') }}">
                                            {{ $dias }}d
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No se encontraron solicitudes con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $quejas->links() }}</div>
    </div>
</div>
