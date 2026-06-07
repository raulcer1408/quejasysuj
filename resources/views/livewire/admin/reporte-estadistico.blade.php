<div>
    {{-- Filtros y exportación --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            @if ($servicioForzado)
                <div class="mb-2">
                    <span class="badge badge-primary px-3 py-1" style="font-size:0.85rem;">
                        <i class="fas fa-building mr-1"></i>
                        Unidad: {{ \App\Models\Queja::SERVICIOS[$servicioForzado] ?? $servicioForzado }}
                    </span>
                </div>
            @endif
            <div class="row align-items-end">
                @if (!$servicioForzado)
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1">Servicio</label>
                    <select wire:model.live="filterServicio" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach ($servicios as $v => $e)
                            <option value="{{ $v }}">{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <label class="small mb-1">Fecha desde</label>
                    <input wire:model.live="fechaDesde" type="date" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <label class="small mb-1">Fecha hasta</label>
                    <input wire:model.live="fechaHasta" type="date" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <label class="small mb-1 d-block">&nbsp;</label>
                    <button wire:click="$set('fechaDesde',''); $set('fechaHasta',''); $set('filterServicio','')"
                            class="btn btn-sm btn-secondary">
                        <i class="fas fa-times mr-1"></i>Limpiar
                    </button>
                </div>
                <div class="col-6 col-md-2 text-right mb-2 mb-md-0 d-flex align-items-end justify-content-end">
                    <button wire:click="exportExcel" class="btn btn-sm btn-success mr-1">
                        <i class="fas fa-file-excel mr-1"></i>
                        <span class="d-none d-sm-inline">Excel</span>
                    </button>
                    <button wire:click="exportPdf" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i>
                        <span class="d-none d-sm-inline">PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Info-boxes: 2×2 en móvil, 4 en línea en escritorio --}}
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-2 mb-md-0">
            <div class="info-box shadow-sm mb-0" style="min-height:60px;">
                <span class="info-box-icon bg-primary d-none d-sm-flex"><i class="fas fa-clipboard-list"></i></span>
                <div class="info-box-content px-2 px-sm-3">
                    <span class="info-box-text small">Total</span>
                    <span class="info-box-number" style="font-size:1.4rem;">{{ $stats['total'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2 mb-md-0">
            <div class="info-box shadow-sm mb-0" style="min-height:60px;">
                <span class="info-box-icon bg-success d-none d-sm-flex"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content px-2 px-sm-3">
                    <span class="info-box-text small">Resueltas</span>
                    <span class="info-box-number" style="font-size:1.4rem;">{{ $stats['resueltas'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2 mb-md-0">
            <div class="info-box shadow-sm mb-0" style="min-height:60px;">
                <span class="info-box-icon bg-danger d-none d-sm-flex"><i class="fas fa-ban"></i></span>
                <div class="info-box-content px-2 px-sm-3">
                    <span class="info-box-text small">No Procede</span>
                    <span class="info-box-number" style="font-size:1.4rem;">{{ $stats['noProcede'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2 mb-md-0">
            <div class="info-box shadow-sm mb-0" style="min-height:60px;">
                <span class="info-box-icon bg-warning d-none d-sm-flex"><i class="fas fa-spinner"></i></span>
                <div class="info-box-content px-2 px-sm-3">
                    <span class="info-box-text small">En Proceso</span>
                    <span class="info-box-number" style="font-size:1.4rem;">{{ $stats['enProceso'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablas: tipo, servicio, estado --}}
    <div class="row">
        <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card card-outline card-danger h-100">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0"><i class="fas fa-tag mr-1"></i>Por Tipo de Solicitud</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-center text-nowrap">Total</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stats['porTipo'] as $etiqueta => $total)
                                    <tr>
                                        <td>{{ $etiqueta }}</td>
                                        <td class="text-center font-weight-bold">{{ $total }}</td>
                                        <td class="text-center text-muted text-nowrap">
                                            {{ $stats['total'] > 0 ? round(($total / $stats['total']) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card card-outline card-primary h-100">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0"><i class="fas fa-cogs mr-1"></i>Por Servicio</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Servicio</th>
                                    <th class="text-center text-nowrap">Total</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stats['porServicio'] as $etiqueta => $total)
                                    @if (!$servicioForzado || $total > 0)
                                    <tr>
                                        <td>{{ $etiqueta }}</td>
                                        <td class="text-center font-weight-bold">{{ $total }}</td>
                                        <td class="text-center text-muted text-nowrap">
                                            {{ $stats['total'] > 0 ? round(($total / $stats['total']) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card card-outline card-info h-100">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0"><i class="fas fa-flag mr-1"></i>Por Estado</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Estado</th>
                                    <th class="text-center text-nowrap">Total</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stats['porEstado'] as $etiqueta => $total)
                                    @if ($total > 0)
                                    <tr>
                                        <td>{{ $etiqueta }}</td>
                                        <td class="text-center font-weight-bold">{{ $total }}</td>
                                        <td class="text-center text-muted text-nowrap">
                                            {{ $stats['total'] > 0 ? round(($total / $stats['total']) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Departamento + Tendencia --}}
    <div class="row mt-3">
        <div class="col-12 col-md-5 mb-3 mb-md-0">
            <div class="card card-outline card-warning">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt mr-1"></i>Por Departamento de Origen
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if (empty($stats['porDepartamento']))
                        <p class="text-muted text-center py-3 small">Sin datos de departamento registrados.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Departamento</th>
                                        <th class="text-center text-nowrap">Total</th>
                                        <th class="text-center">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats['porDepartamento'] as $dep => $total)
                                        <tr>
                                            <td>{{ $dep }}</td>
                                            <td class="text-center font-weight-bold">{{ $total }}</td>
                                            <td class="text-center text-muted text-nowrap">
                                                {{ $stats['total'] > 0 ? round(($total / $stats['total']) * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-7 mb-3 mb-md-0">
            <div class="card card-outline card-success">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line mr-1"></i>Tendencia Mensual
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if ($stats['tendencia']->isEmpty())
                        <p class="text-muted text-center py-3 small">Sin datos en el período seleccionado.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-center text-nowrap">Solicitudes</th>
                                        <th class="d-none d-sm-table-cell">Barra</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $maxMes = $stats['tendencia']->max('total'); @endphp
                                    @foreach ($stats['tendencia'] as $t)
                                        <tr>
                                            <td class="text-nowrap">{{ $t['mes'] }}</td>
                                            <td class="text-center font-weight-bold">{{ $t['total'] }}</td>
                                            <td class="d-none d-sm-table-cell" style="width:50%;">
                                                @php $pct = $maxMes > 0 ? round(($t['total'] / $maxMes) * 100) : 0; @endphp
                                                <div style="background:#c8102e;height:12px;width:{{ $pct }}%;border-radius:2px;min-width:4px;"></div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tasa de resolución --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-outline card-success">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-percentage mr-1"></i>Tasa de Resolución
                    </h6>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex align-items-center flex-wrap" style="gap:12px;">
                        <div class="font-weight-bold flex-shrink-0" style="font-size:28px;color:#c8102e;">
                            {{ $stats['tasaExito'] }}%
                        </div>
                        <div class="flex-grow-1" style="min-width:150px;">
                            <div class="progress" style="height:20px;">
                                <div class="progress-bar bg-success"
                                     style="width:{{ $stats['tasaExito'] }}%;">
                                    <span class="d-none d-sm-inline">{{ $stats['tasaExito'] }}% resueltas</span>
                                </div>
                                @if ($stats['noProcede'] > 0 && $stats['total'] > 0)
                                    <div class="progress-bar bg-danger"
                                         style="width:{{ round(($stats['noProcede'] / $stats['total']) * 100, 1) }}%;">
                                        <span class="d-none d-sm-inline">
                                            {{ round(($stats['noProcede'] / $stats['total']) * 100, 1) }}% no procede
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap mt-1" style="gap:8px;font-size:0.8rem;">
                                <span class="text-success">
                                    <i class="fas fa-circle mr-1" style="font-size:8px;"></i>
                                    {{ $stats['tasaExito'] }}% resueltas
                                </span>
                                @if ($stats['noProcede'] > 0 && $stats['total'] > 0)
                                    <span class="text-danger">
                                        <i class="fas fa-circle mr-1" style="font-size:8px;"></i>
                                        {{ round(($stats['noProcede'] / $stats['total']) * 100, 1) }}% no procede
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
