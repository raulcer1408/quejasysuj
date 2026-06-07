<div>
    {{-- Alertas --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <div class="input-group">
                        <input wire:model.live.debounce.300ms="search"
                               type="text"
                               class="form-control"
                               placeholder="Buscar por nombre o correo...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-8 col-md-4 mb-0">
                    <select wire:model.live="filterRole" class="form-control">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $valor => $etiqueta)
                            @if ($valor !== 'visitante')
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-4 col-md-2 text-right d-flex align-items-center justify-content-end">
                    <span class="badge badge-info">
                        {{ $usuarios->total() }} usuario(s)
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cabecera compartida --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-users mr-1"></i>Usuarios del Sistema
        </h6>
        <button wire:click="openCreateModal" class="btn btn-sm btn-success">
            <i class="fas fa-user-plus mr-1"></i><span class="d-none d-sm-inline">Nuevo Usuario</span>
        </button>
    </div>

    @php
        $colores = [
            'superusuario'     => 'danger',
            'sistemas'         => 'warning',
            'jefe_formacion'      => 'info',
            'jefe_capacitacion'   => 'info',
            'jefe_administrativo' => 'info',
            'jefe_unidad'         => 'info',
            'pedagoga_formacion'    => 'purple',
            'pedagoga_capacitacion' => 'purple',
            'responsable_revista'   => 'purple',
            'responsable_administrativo' => 'purple',
            'pedagoga'              => 'purple',
            'coordinador_academico_capacitacion' => 'primary',
            'coordinador'      => 'primary',
            'docente_interno'  => 'success',
            'docente'          => 'success',
            'visitante'        => 'secondary',
        ];
    @endphp

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($usuarios as $usuario)
            @php $color = $colores[$usuario->role] ?? 'secondary'; @endphp
            <div class="card mb-2 shadow-sm">
                <div class="card-body py-2 px-3">
                    {{-- Fila 1: foto + nombre + estado --}}
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center">
                            <img src="{{ $usuario->profile_photo_url }}"
                                 class="img-circle elevation-1 mr-2"
                                 style="width:34px;height:34px;object-fit:cover;"
                                 alt="{{ $usuario->name }}">
                            <div>
                                <div class="font-weight-bold" style="font-size:0.88rem;line-height:1.2;">
                                    {{ $usuario->name }}
                                </div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $usuario->email }}</div>
                            </div>
                        </div>
                        @if ($usuario->activo)
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-times-circle"></i></span>
                        @endif
                    </div>
                    {{-- Fila 2: rol + comisión + departamento --}}
                    <div class="d-flex flex-wrap align-items-center mb-2" style="gap:4px;">
                        <span class="badge badge-{{ $color }}" style="font-size:10px;">
                            {{ $roles[$usuario->role] ?? $usuario->role }}
                        </span>
                        @if ($usuario->es_miembro_comision)
                            <span class="badge badge-purple" style="font-size:10px;">
                                <i class="fas fa-book mr-1"></i>Com. Revista
                            </span>
                        @endif
                        @if ($usuario->departamento)
                            <span class="badge badge-light border" style="font-size:10px;color:#555;">
                                {{ \App\Models\User::DEPARTAMENTOS[$usuario->departamento] ?? $usuario->departamento }}
                            </span>
                        @endif
                        <small class="text-muted ml-auto">{{ $usuario->created_at->format('d/m/Y') }}</small>
                    </div>
                    {{-- Fila 3: acciones --}}
                    <div class="d-flex" style="gap:4px;">
                        <button wire:click="openEditModal({{ $usuario->id }})"
                                class="btn btn-xs btn-warning flex-fill" title="Editar datos">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="openModal({{ $usuario->id }})"
                                class="btn btn-xs btn-info flex-fill" title="Cambiar rol">
                            <i class="fas fa-user-edit"></i>
                        </button>
                        @canany(['administrar-usuarios'])
                        <button wire:click="openPasswordModal({{ $usuario->id }})"
                                class="btn btn-xs btn-danger flex-fill" title="Resetear contraseña">
                            <i class="fas fa-key"></i>
                        </button>
                        <button wire:click="toggleActivo({{ $usuario->id }})"
                                class="btn btn-xs {{ $usuario->activo ? 'btn-secondary' : 'btn-success' }} flex-fill"
                                title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}"
                                wire:confirm="{{ $usuario->activo ? '¿Desactivar este usuario?' : '¿Activar este usuario?' }}">
                            <i class="fas {{ $usuario->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                        </button>
                        @if ($usuario->id !== auth()->id())
                        <button wire:click="openDeleteModal({{ $usuario->id }})"
                                class="btn btn-xs btn-outline-danger flex-fill" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                        @endcanany
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                No se encontraron usuarios.
            </div>
        @endforelse
        <div class="mt-2">{{ $usuarios->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-primary d-none d-md-block">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th class="text-center">Com. Revista</th>
                        <th>Departamento</th>
                        <th class="text-center">Estado</th>
                        <th>Registrado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        @php $color = $colores[$usuario->role] ?? 'secondary'; @endphp
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $usuario->profile_photo_url }}"
                                         class="img-circle elevation-2 mr-2"
                                         style="width:32px;height:32px;object-fit:cover;"
                                         alt="{{ $usuario->name }}">
                                    {{ $usuario->name }}
                                </div>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge badge-{{ $color }}"
                                      style="white-space:normal;word-break:break-word;display:inline-block;max-width:130px;font-size:10px;line-height:1.3;">
                                    {{ $roles[$usuario->role] ?? $usuario->role }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($usuario->es_miembro_comision)
                                    <span class="badge badge-purple" title="Miembro de Comisión de Revista">
                                        <i class="fas fa-check mr-1"></i>Sí
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:12px;">—</span>
                                @endif
                            </td>
                            <td>{{ \App\Models\User::DEPARTAMENTOS[$usuario->departamento] ?? '—' }}</td>
                            <td class="text-center">
                                @if ($usuario->activo)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-times-circle mr-1"></i>Inactivo
                                    </span>
                                @endif
                            </td>
                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td class="text-center text-nowrap">
                                <button wire:click="openEditModal({{ $usuario->id }})"
                                        class="btn btn-xs btn-warning"
                                        style="padding:2px 6px;"
                                        title="Editar datos">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="openModal({{ $usuario->id }})"
                                        class="btn btn-xs btn-info"
                                        style="padding:2px 6px;"
                                        title="Cambiar rol">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                                @canany(['administrar-usuarios'])
                                <button wire:click="openPasswordModal({{ $usuario->id }})"
                                        class="btn btn-xs btn-danger"
                                        style="padding:2px 6px;"
                                        title="Resetear contraseña">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button wire:click="toggleActivo({{ $usuario->id }})"
                                        class="btn btn-xs {{ $usuario->activo ? 'btn-secondary' : 'btn-success' }}"
                                        style="padding:2px 6px;"
                                        title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}"
                                        wire:confirm="{{ $usuario->activo ? '¿Desactivar este usuario?' : '¿Activar este usuario?' }}">
                                    <i class="fas {{ $usuario->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                </button>
                                @if ($usuario->id !== auth()->id())
                                <button wire:click="openDeleteModal({{ $usuario->id }})"
                                        class="btn btn-xs btn-outline-danger"
                                        style="padding:2px 6px;"
                                        title="Eliminar usuario">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                                @endcanany
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $usuarios->links() }}
        </div>
    </div>

    {{-- Modal cambiar rol --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title">
                            <i class="fas fa-user-edit mr-2"></i>Cambiar Rol de Usuario
                        </h5>
                        <button type="button" class="close" wire:click="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <p class="font-weight-bold mb-0">{{ $editingName }}</p>
                        </div>
                        <div class="form-group">
                            <label>Seleccionar nuevo rol</label>
                            <select wire:model.live="editingRole" class="form-control">
                                <option value="">-- Seleccionar rol --</option>
                                @foreach ($roles as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            @error('editingRole')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        @if (in_array($editingRole, $rolesConUnidad))
                        <div class="form-group">
                            <label>Unidad asignada <span class="text-danger">*</span></label>
                            <select wire:model="editingUnidad" class="form-control">
                                <option value="">-- Seleccionar unidad --</option>
                                @foreach ($unidades as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            @error('editingUnidad')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Departamento</label>
                            <select wire:model="editingDepartamento" class="form-control">
                                <option value="">-- Seleccionar departamento --</option>
                                @foreach ($departamentos as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="editingEsMiembroComision"
                                       wire:model="editingEsMiembroComision">
                                <label class="custom-control-label font-weight-bold" for="editingEsMiembroComision">
                                    <i class="fas fa-book mr-1 text-purple"></i>
                                    Miembro de Comisión de Revista
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1 ml-4">
                                Permite al usuario revisar quejas y sugerencias dirigidas al servicio de Investigación (Revista), independientemente de su rol principal.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="saveRole">
                            <i class="fas fa-save mr-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal eliminar usuario --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-trash mr-2"></i>Eliminar Usuario
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeDeleteModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p class="mb-1">¿Está seguro de eliminar al usuario?</p>
                        <p class="font-weight-bold mb-3">{{ $deleteUserName }}</p>
                        <div class="alert alert-warning text-left py-2 mb-0">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Esta acción es <strong>irreversible</strong>. Se eliminarán todos los datos asociados al usuario.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteUser">
                            <i class="fas fa-trash mr-1"></i>Eliminar definitivamente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal crear usuario --}}
    @if ($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus mr-2"></i>Crear Nuevo Usuario
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeCreateModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre completo <span class="text-danger">*</span></label>
                                    <input wire:model="createName" type="text"
                                           class="form-control @error('createName') is-invalid @enderror"
                                           placeholder="Nombre completo"
                                           autocomplete="off">
                                    @error('createName')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Correo electrónico <span class="text-danger">*</span></label>
                                    <input wire:model="createEmail" type="email"
                                           class="form-control @error('createEmail') is-invalid @enderror"
                                           placeholder="correo@ejemplo.com"
                                           autocomplete="off">
                                    @error('createEmail')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rol <span class="text-danger">*</span></label>
                                    <select wire:model.live="createRole"
                                            class="form-control @error('createRole') is-invalid @enderror">
                                        <option value="">-- Seleccionar rol --</option>
                                        @foreach ($roles as $valor => $etiqueta)
                                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    @error('createRole')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if (in_array($createRole, $rolesConUnidad))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Unidad <span class="text-danger">*</span></label>
                                    <select wire:model="createUnidad"
                                            class="form-control @error('createUnidad') is-invalid @enderror">
                                        <option value="">-- Seleccionar unidad --</option>
                                        @foreach ($unidades as $valor => $etiqueta)
                                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                    @error('createUnidad')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Departamento</label>
                                    <select wire:model="createDepartamento" class="form-control">
                                        <option value="">-- Seleccionar departamento --</option>
                                        @foreach ($departamentos as $valor => $etiqueta)
                                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="createAutoGenerate"
                                       wire:model.live="createAutoGenerate">
                                <label class="custom-control-label" for="createAutoGenerate">
                                    Generar contraseña automáticamente
                                </label>
                            </div>
                        </div>

                        @if ($createAutoGenerate && $createGeneratedPassword)
                            <div class="alert alert-info">
                                <i class="fas fa-lock mr-1"></i>
                                <strong>Contraseña generada:</strong>
                                <code class="ml-1">{{ $createGeneratedPassword }}</code>
                                <small class="d-block mt-1 text-muted">Anote esta contraseña antes de guardar.</small>
                            </div>
                        @endif

                        @if (!$createAutoGenerate)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contraseña <span class="text-danger">*</span></label>
                                        <input wire:model="createPassword" type="password"
                                               class="form-control @error('createPassword') is-invalid @enderror"
                                               placeholder="Mínimo 8 caracteres"
                                               autocomplete="new-password">
                                        @error('createPassword')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirmar contraseña <span class="text-danger">*</span></label>
                                        <input wire:model="createPasswordConfirmation" type="password"
                                               class="form-control"
                                               placeholder="Repetir contraseña"
                                               autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="alert alert-success py-2 mb-0">
                            <i class="fas fa-check-circle mr-1"></i>
                            El usuario quedará <strong>verificado y activo</strong> inmediatamente, sin requerir confirmación de correo.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" wire:click="createUser">
                            <i class="fas fa-user-plus mr-1"></i>Crear Usuario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal resetear contraseña --}}
    @if ($showPasswordModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-key mr-2"></i>Restablecer Contraseña
                        </h5>
                        <button type="button" class="close text-white" wire:click="closePasswordModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-user mr-1"></i>
                            <strong>{{ $passwordUserName }}</strong>
                        </div>

                        {{-- Generar automáticamente --}}
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="autoGenerate"
                                       wire:model.live="autoGenerate">
                                <label class="custom-control-label" for="autoGenerate">
                                    Generar contraseña automáticamente
                                </label>
                            </div>
                        </div>

                        {{-- Contraseña generada --}}
                        @if ($autoGenerate && $generatedPassword)
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-lock mr-1"></i>
                                    <strong>Contraseña generada:</strong>
                                    <code class="ml-1">{{ $generatedPassword }}</code>
                                </span>
                            </div>
                        @endif

                        {{-- Campos manuales --}}
                        @if (!$autoGenerate)
                            <div class="form-group">
                                <label>Nueva contraseña</label>
                                <input wire:model="newPassword"
                                       type="password"
                                       class="form-control @error('newPassword') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres">
                                @error('newPassword')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <input wire:model="newPasswordConfirmation"
                                       type="password"
                                       class="form-control"
                                       placeholder="Repetir contraseña">
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closePasswordModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="savePassword">
                            <i class="fas fa-key mr-1"></i>Restablecer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal editar datos del usuario --}}
    @if ($showEditModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-edit mr-2"></i>Editar Datos del Usuario
                        </h5>
                        <button type="button" class="close" wire:click="closeEditModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre completo <span class="text-danger">*</span></label>
                            <input wire:model="editDataName" type="text"
                                   class="form-control @error('editDataName') is-invalid @enderror"
                                   placeholder="Nombre completo">
                            @error('editDataName')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Correo electrónico <span class="text-danger">*</span></label>
                            <input wire:model="editDataEmail" type="email"
                                   class="form-control @error('editDataEmail') is-invalid @enderror"
                                   placeholder="correo@ejemplo.com">
                            @error('editDataEmail')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Departamento</label>
                            <select wire:model="editDataDepartamento" class="form-control">
                                <option value="">-- Sin departamento --</option>
                                @foreach ($departamentos as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeEditModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="saveEdit">
                            <i class="fas fa-save mr-1"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
