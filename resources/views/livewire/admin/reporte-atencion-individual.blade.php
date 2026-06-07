<div>
    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row align-items-end">
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Desde</label>
                    <input wire:model.live="fechaDesde" type="date" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Hasta</label>
                    <input wire:model.live="fechaHasta" type="date" class="form-control form-control-sm">
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
                            @foreach ($servicios as $v => $e)
                                <option value="{{ $v }}">{{ $e }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <label class="small mb-1">Actor específico</label>
                    <select wire:model.live="filterUserId" class="form-control form-control-sm">
                        <option value="0">Todos los actores</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1 mb-2 mb-md-0 d-flex align-items-end">
                    <button wire:click="$set('fechaDesde',''); $set('fechaHasta',''); $set('filterServicio',''); $set('filterUserId', 0)"
                            class="btn btn-sm btn-secondary w-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="col-6 col-md-2 d-flex align-items-end" style="gap:4px;">
                    <button wire:click="exportExcel" class="btn btn-sm btn-success flex-fill">
                        <i class="fas fa-file-excel"></i><span class="d-none d-sm-inline ml-1">Excel</span>
                    </button>
                    <button wire:click="exportPdf" class="btn btn-sm btn-danger flex-fill">
                        <i class="fas fa-file-pdf"></i><span class="d-none d-sm-inline ml-1">PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen por actor --}}
    <div class="card card-outline card-danger mb-3">
        <div class="card-header py-2">
            <h6 class="card-title mb-0" style="font-size:0.88rem;">
                <i class="fas fa-users mr-1"></i>Resumen por Actor
                <span class="badge badge-secondary ml-1">{{ count($resumen) }} actores</span>
            </h6>
        </div>
        <div class="card-body p-0">
            @if (empty($resumen))
                <p class="text-center text-muted py-4 small">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    No hay intervenciones en el período seleccionado.
                </p>
            @else
                @php $maxProm = collect($resumen)->max('promedio_horas'); @endphp

                {{-- Vista móvil — tarjetas por actor --}}
                <div class="d-md-none p-2">
                    @foreach ($resumen as $r)
                        @php
                            $pct   = $maxProm > 0 ? round(($r['promedio_horas'] / $maxProm) * 100) : 0;
                            $color = $r['promedio_dias'] <= 1 ? '#28a745'
                                   : ($r['promedio_dias'] <= 3 ? '#ffc107' : '#dc3545');
                            $badge = $r['promedio_dias'] <= 1 ? 'success'
                                   : ($r['promedio_dias'] <= 3 ? 'warning' : 'danger');
                        @endphp
                        <div class="card mb-2 shadow-sm">
                            <div class="card-body py-2 px-3">
                                {{-- Nombre + promedio --}}
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="font-weight-bold" style="font-size:0.88rem;">{{ $r['user']->name }}</div>
                                    <span class="badge badge-{{ $badge }}" style="font-size:11px;">
                                        {{ $r['promedio_horas'] }}h ({{ $r['promedio_dias'] }}d)
                                    </span>
                                </div>
                                {{-- Roles --}}
                                <div class="mb-2" style="gap:3px;display:flex;flex-wrap:wrap;">
                                    @foreach ($r['roles'] as $rol)
                                        <span class="badge badge-secondary" style="font-size:10px;">{{ $rol }}</span>
                                    @endforeach
                                </div>
                                {{-- Stats --}}
                                <div class="row text-center mb-2" style="font-size:0.78rem;">
                                    <div class="col-4">
                                        <div class="text-muted" style="font-size:0.68rem;">Intervenciones</div>
                                        <div class="font-weight-bold">{{ $r['intervenciones'] }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted" style="font-size:0.68rem;">Mín.</div>
                                        <div>{{ $r['horas_min'] }}h</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted" style="font-size:0.68rem;">Máx.</div>
                                        <div>{{ $r['horas_max'] }}h</div>
                                    </div>
                                </div>
                                {{-- Barra de velocidad --}}
                                <div style="background:#e9ecef;border-radius:3px;height:8px;">
                                    <div style="background:{{ $color }};width:{{ $pct }}%;height:8px;border-radius:3px;"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="px-1 pt-1">
                        <small class="text-muted">
                            <span style="color:#28a745;">■</span> ≤ 1 día &nbsp;
                            <span style="color:#ffc107;">■</span> ≤ 3 días &nbsp;
                            <span style="color:#dc3545;">■</span> > 3 días
                        </small>
                    </div>
                </div>

                {{-- Vista escritorio — tabla --}}
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0" style="font-size:12.5px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Actor</th>
                                    <th>Rol en el flujo</th>
                                    <th class="text-center">Intervenciones</th>
                                    <th class="text-center">Mín. respuesta</th>
                                    <th class="text-center">Máx. respuesta</th>
                                    <th class="text-center">Promedio</th>
                                    <th style="width:25%;">Velocidad relativa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resumen as $r)
                                    @php
                                        $pct   = $maxProm > 0 ? round(($r['promedio_horas'] / $maxProm) * 100) : 0;
                                        $color = $r['promedio_dias'] <= 1 ? '#28a745'
                                               : ($r['promedio_dias'] <= 3 ? '#ffc107' : '#dc3545');
                                    @endphp
                                    <tr>
                                        <td class="font-weight-bold">{{ $r['user']->name }}</td>
                                        <td>
                                            @foreach ($r['roles'] as $rol)
                                                <span class="badge badge-secondary" style="font-size:10px;">{{ $rol }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">{{ $r['intervenciones'] }}</td>
                                        <td class="text-center text-muted small">
                                            {{ $r['horas_min'] }}h
                                            <span class="text-muted">({{ round($r['horas_min']/24,1) }}d)</span>
                                        </td>
                                        <td class="text-center text-muted small">
                                            {{ $r['horas_max'] }}h
                                            <span class="text-muted">({{ round($r['horas_max']/24,1) }}d)</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-weight-bold" style="color:{{ $color }};">
                                                {{ $r['promedio_horas'] }}h
                                            </span>
                                            <small class="text-muted">({{ $r['promedio_dias'] }}d)</small>
                                        </td>
                                        <td>
                                            <div style="background:#e9ecef;border-radius:3px;height:12px;">
                                                <div style="background:{{ $color }};width:{{ $pct }}%;height:12px;border-radius:3px;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 py-1">
                        <small class="text-muted">
                            <span style="color:#28a745;">■</span> ≤ 1 día &nbsp;
                            <span style="color:#ffc107;">■</span> ≤ 3 días &nbsp;
                            <span style="color:#dc3545;">■</span> > 3 días
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Detalle de intervenciones --}}
    <div class="card card-outline card-primary">
        <div class="card-header py-2">
            <h6 class="card-title mb-0" style="font-size:0.88rem;">
                <i class="fas fa-list-alt mr-1"></i>Detalle de Intervenciones
                <span class="badge badge-secondary ml-1">{{ $total }}</span>
            </h6>
        </div>

        {{-- Vista móvil — tarjetas --}}
        <div class="card-body p-2 d-md-none">
            @forelse ($filasPag as $item)
                @php
                    $color = $item['dias'] <= 1 ? 'success'
                           : ($item['dias'] <= 3 ? 'warning' : 'danger');
                @endphp
                <div class="card mb-2 shadow-sm">
                    <div class="card-body py-2 px-3">
                        {{-- Fila 1: actor + tiempo --}}
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="font-weight-bold" style="font-size:0.85rem;">{{ $item['user']->name }}</div>
                            <span class="badge badge-{{ $color }}" style="font-size:11px;">
                                {{ $item['horas'] }}h ({{ $item['dias'] }}d)
                            </span>
                        </div>
                        {{-- Fila 2: rol + solicitud + tipo --}}
                        <div class="d-flex flex-wrap align-items-center mb-1" style="gap:4px;">
                            <span class="badge badge-secondary" style="font-size:10px;">{{ $item['rol'] }}</span>
                            <span class="badge badge-dark" style="font-size:10px;">#{{ str_pad($item['queja']->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="badge {{ $item['queja']->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}" style="font-size:10px;">
                                {{ \App\Models\Queja::TIPOS[$item['queja']->tipo_solicitud] ?? $item['queja']->tipo_solicitud }}
                            </span>
                            <span class="text-muted" style="font-size:0.75rem;">
                                {{ \App\Models\Queja::SERVICIOS[$item['queja']->servicio] ?? $item['queja']->servicio }}
                            </span>
                        </div>
                        {{-- Fila 3: fechas --}}
                        <div class="d-flex justify-content-between" style="font-size:0.72rem;color:#888;">
                            <span><i class="fas fa-arrow-down mr-1"></i>{{ $item['recibido_en']->format('d/m/Y H:i') }}</span>
                            <span><i class="fas fa-arrow-up mr-1"></i>{{ $item['respondio_en']->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    Sin intervenciones registradas.
                </div>
            @endforelse
        </div>

        {{-- Vista escritorio — tabla --}}
        <div class="card-body p-0 d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0" style="font-size:13.5px;">
                    <thead class="thead-dark">
                        <tr>
                            <th>Actor</th>
                            <th>Rol</th>
                            <th class="text-center">N° Sol.</th>
                            <th>Servicio</th>
                            <th>Tipo</th>
                            <th class="text-center">Recibió</th>
                            <th class="text-center">Respondió</th>
                            <th class="text-center">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filasPag as $item)
                            @php
                                $color = $item['dias'] <= 1 ? 'success'
                                       : ($item['dias'] <= 3 ? 'warning' : 'danger');
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ $item['user']->name }}</td>
                                <td><span class="badge badge-secondary" style="font-size:10px;">{{ $item['rol'] }}</span></td>
                                <td class="text-center">
                                    <span class="badge badge-dark">
                                        #{{ str_pad($item['queja']->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>{{ \App\Models\Queja::SERVICIOS[$item['queja']->servicio] ?? $item['queja']->servicio }}</td>
                                <td>
                                    <span class="badge {{ $item['queja']->tipo_solicitud === 'queja' ? 'badge-danger' : 'badge-info' }}" style="font-size:10px;">
                                        {{ \App\Models\Queja::TIPOS[$item['queja']->tipo_solicitud] ?? $item['queja']->tipo_solicitud }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">{{ $item['recibido_en']->format('d/m/Y H:i') }}</td>
                                <td class="text-center text-muted">{{ $item['respondio_en']->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $color }}">
                                        {{ $item['horas'] }}h
                                    </span>
                                    <small class="text-muted">({{ $item['dias'] }}d)</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Sin intervenciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
