<div>
    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <input wire:model.live.debounce.400ms="search"
                           type="text" class="form-control form-control-sm"
                           placeholder="Buscar por usuario o descripción...">
                </div>
                @if ($tab === 'quejas')
                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <input wire:model.live.debounce.400ms="searchNumero"
                           type="number" min="1" class="form-control form-control-sm"
                           placeholder="N° solicitud...">
                </div>
                @endif
                <div class="col-6 col-md-{{ $tab === 'quejas' ? '2' : '3' }} mb-2 mb-md-0">
                    <input wire:model.live="fechaDesde" type="date"
                           class="form-control form-control-sm" title="Desde">
                </div>
                <div class="col-6 col-md-{{ $tab === 'quejas' ? '2' : '3' }} mb-2 mb-md-0">
                    <input wire:model.live="fechaHasta" type="date"
                           class="form-control form-control-sm" title="Hasta">
                </div>
                <div class="col-6 col-md-2 text-right">
                    <button wire:click="$set('search',''); $set('fechaDesde',''); $set('fechaHasta',''); $set('searchNumero','')"
                            class="btn btn-sm btn-secondary btn-block d-md-inline-block" style="max-width:120px;">
                        <i class="fas fa-times mr-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card card-outline card-danger">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a href="#" wire:click.prevent="switchTab('sistema')"
                       class="nav-link {{ $tab === 'sistema' ? 'active' : '' }}">
                        <i class="fas fa-cogs mr-1"></i>
                        <span class="d-none d-sm-inline">Acciones del </span>Sistema
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" wire:click.prevent="switchTab('quejas')"
                       class="nav-link {{ $tab === 'quejas' ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        <span class="d-none d-sm-inline">Flujo de </span>Solicitudes
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">

            {{-- ══ Tab: Acciones del Sistema ══ --}}
            @if ($tab === 'sistema')
                @if ($registros->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        No hay registros de actividad del sistema.
                    </div>
                @else
                    @php
                        $iconos = [
                            'Creó usuario'           => ['icon' => 'fa-user-plus',  'color' => 'success'],
                            'Cambió rol'             => ['icon' => 'fa-user-tag',   'color' => 'primary'],
                            'Eliminó usuario'        => ['icon' => 'fa-user-times', 'color' => 'danger'],
                            'Activó usuario'         => ['icon' => 'fa-toggle-on',  'color' => 'success'],
                            'Desactivó usuario'      => ['icon' => 'fa-toggle-off', 'color' => 'warning'],
                            'Restableció contraseña' => ['icon' => 'fa-key',        'color' => 'secondary'],
                        ];
                    @endphp

                    {{-- Móvil --}}
                    <div class="d-md-none p-2">
                        @foreach ($registros as $log)
                            @php $meta = $iconos[$log->accion] ?? ['icon' => 'fa-circle', 'color' => 'secondary']; @endphp
                            <div class="card mb-2 shadow-sm">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="badge badge-{{ $meta['color'] }}" style="font-size:10px;">
                                            <i class="fas {{ $meta['icon'] }} mr-1"></i>{{ $log->accion }}
                                        </span>
                                        <small class="text-muted" style="font-size:0.72rem;">
                                            {{ $log->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="font-weight-bold" style="font-size:0.85rem;">{{ $log->user->name }}</div>
                                    <div class="text-muted mb-1" style="font-size:0.75rem;">{{ $log->user->email }}</div>
                                    <div style="font-size:0.82rem;">{{ $log->descripcion }}</div>
                                    @if ($log->ip)
                                        <div class="text-muted mt-1" style="font-size:0.72rem;">
                                            <i class="fas fa-network-wired mr-1"></i>{{ $log->ip }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-2">{{ $registros->links() }}</div>
                    </div>

                    {{-- Escritorio --}}
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:160px">Fecha y hora</th>
                                        <th>Usuario</th>
                                        <th style="width:200px">Acción</th>
                                        <th>Descripción</th>
                                        <th style="width:130px">IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registros as $log)
                                        @php $meta = $iconos[$log->accion] ?? ['icon' => 'fa-circle', 'color' => 'secondary']; @endphp
                                        <tr>
                                            <td class="text-nowrap text-muted small">
                                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td>
                                                <span class="font-weight-bold">{{ $log->user->name }}</span>
                                                <br><small class="text-muted">{{ $log->user->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $meta['color'] }}">
                                                    <i class="fas {{ $meta['icon'] }} mr-1"></i>{{ $log->accion }}
                                                </span>
                                            </td>
                                            <td class="small">{{ $log->descripcion }}</td>
                                            <td class="text-muted small">{{ $log->ip ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-3 py-2">{{ $registros->links() }}</div>
                    </div>
                @endif

            {{-- ══ Tab: Flujo de Solicitudes ══ --}}
            @else
                @if ($registros->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        No hay registros de flujo de solicitudes.
                    </div>
                @else
                    @php
                        $coloresBadge = [
                            'pendiente'            => 'warning',
                            'en_revision'          => 'info',
                            'derivado_coordinador' => 'primary',
                            'respondido'           => 'secondary',
                            'en_validacion'        => 'purple',
                            'resuelto'             => 'success',
                            'no_procede'           => 'danger',
                        ];
                        $etiquetas = \App\Models\Queja::ESTADOS;
                    @endphp

                    {{-- Móvil --}}
                    <div class="d-md-none p-2">
                        @foreach ($registros as $seg)
                            <div class="card mb-2 shadow-sm">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <span class="badge badge-dark" style="font-size:10px;">
                                                #{{ str_pad($seg->queja_id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <span class="badge badge-{{ $coloresBadge[$seg->estado_nuevo] ?? 'secondary' }} ml-1"
                                                  style="font-size:10px;">
                                                {{ $etiquetas[$seg->estado_nuevo] ?? $seg->estado_nuevo }}
                                            </span>
                                        </div>
                                        <small class="text-muted" style="font-size:0.72rem;">
                                            {{ $seg->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="font-weight-bold" style="font-size:0.85rem;">{{ $seg->user->name }}</div>
                                    <div class="text-muted mb-1" style="font-size:0.75rem;">{{ $seg->user->email }}</div>
                                    <div style="font-size:0.82rem;">{{ $seg->accion }}</div>
                                    @if ($seg->comentario)
                                        <div class="text-muted mt-1 border-left border-secondary pl-2"
                                             style="font-size:0.78rem;">{{ $seg->comentario }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-2">{{ $registros->links() }}</div>
                    </div>

                    {{-- Escritorio --}}
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:160px">Fecha y hora</th>
                                        <th style="width:80px">N° Sol.</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                        <th style="width:160px">Estado resultante</th>
                                        <th>Comentario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registros as $seg)
                                        <tr>
                                            <td class="text-nowrap text-muted small">
                                                {{ $seg->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-dark">
                                                    #{{ str_pad($seg->queja_id, 5, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold">{{ $seg->user->name }}</span>
                                                <br><small class="text-muted">{{ $seg->user->email }}</small>
                                            </td>
                                            <td class="small">{{ $seg->accion }}</td>
                                            <td>
                                                <span class="badge badge-{{ $coloresBadge[$seg->estado_nuevo] ?? 'secondary' }}">
                                                    {{ $etiquetas[$seg->estado_nuevo] ?? $seg->estado_nuevo }}
                                                </span>
                                            </td>
                                            <td class="small text-muted">{{ $seg->comentario ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-3 py-2">{{ $registros->links() }}</div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
