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

    {{-- Filtro --}}
    <div class="card card-outline card-secondary mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-8 col-md-9 mb-0">
                    <div class="input-group">
                        <input wire:model.live.debounce.300ms="search"
                               type="text" class="form-control"
                               placeholder="Buscar por nombre o correo...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-md-3 text-right d-flex align-items-center justify-content-end">
                    <span class="badge badge-secondary" style="font-size:12px;">
                        <i class="fas fa-eye mr-1"></i>{{ $usuarios->total() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Cabecera --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 text-muted">
            <i class="fas fa-eye mr-1"></i>Usuarios Visitantes
        </h6>
        <button wire:click="openCreateModal" class="btn btn-sm btn-success">
            <i class="fas fa-user-plus mr-1"></i><span class="d-none d-sm-inline">Nuevo Visitante</span>
        </button>
    </div>

    {{-- Vista móvil — tarjetas --}}
    <div class="d-md-none">
        @forelse ($usuarios as $usuario)
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
                    {{-- Fila 2: departamento + fecha --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge badge-light border" style="font-size:10px;color:#555;">
                            {{ \App\Models\User::DEPARTAMENTOS[$usuario->departamento] ?? '—' }}
                        </span>
                        <small class="text-muted">{{ $usuario->created_at->format('d/m/Y') }}</small>
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
                        <button wire:click="openPasswordModal({{ $usuario->id }})"
                                class="btn btn-xs btn-danger flex-fill" title="Restablecer contraseña">
                            <i class="fas fa-key"></i>
                        </button>
                        <button wire:click="toggleActivo({{ $usuario->id }})"
                                class="btn btn-xs {{ $usuario->activo ? 'btn-secondary' : 'btn-success' }} flex-fill"
                                title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}"
                                wire:confirm="{{ $usuario->activo ? '¿Desactivar este visitante?' : '¿Activar este visitante?' }}">
                            <i class="fas {{ $usuario->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                        </button>
                        <button wire:click="openDeleteModal({{ $usuario->id }})"
                                class="btn btn-xs btn-outline-danger flex-fill" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-eye-slash fa-2x mb-2 d-block"></i>
                No se encontraron usuarios visitantes.
            </div>
        @endforelse
        <div class="mt-2">{{ $usuarios->links() }}</div>
    </div>

    {{-- Vista escritorio — tabla --}}
    <div class="card card-outline card-secondary d-none d-md-block">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Departamento</th>
                        <th class="text-center">Estado</th>
                        <th>Registrado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
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
                                        class="btn btn-xs btn-warning" style="padding:2px 6px;"
                                        title="Editar datos">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="openModal({{ $usuario->id }})"
                                        class="btn btn-xs btn-info" style="padding:2px 6px;"
                                        title="Cambiar rol">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                                <button wire:click="openPasswordModal({{ $usuario->id }})"
                                        class="btn btn-xs btn-danger" style="padding:2px 6px;"
                                        title="Restablecer contraseña">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button wire:click="toggleActivo({{ $usuario->id }})"
                                        class="btn btn-xs {{ $usuario->activo ? 'btn-secondary' : 'btn-success' }}"
                                        style="padding:2px 6px;"
                                        title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}"
                                        wire:confirm="{{ $usuario->activo ? '¿Desactivar este visitante?' : '¿Activar este visitante?' }}">
                                    <i class="fas {{ $usuario->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                </button>
                                <button wire:click="openDeleteModal({{ $usuario->id }})"
                                        class="btn btn-xs btn-outline-danger" style="padding:2px 6px;"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-eye-slash fa-2x mb-2 d-block"></i>
                                No se encontraron usuarios visitantes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $usuarios->links() }}</div>
    </div>

    {{-- Modal cambiar rol --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-user-edit mr-2"></i>Cambiar Rol — {{ $editingName }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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
                        <div class="form-group mb-0">
                            <label>Departamento</label>
                            <select wire:model="editingDepartamento" class="form-control">
                                <option value="">-- Seleccionar departamento --</option>
                                @foreach ($departamentos as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-info" wire:click="saveRole">
                            <i class="fas fa-save mr-1"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal restablecer contraseña --}}
    @if ($showPasswordModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-key mr-2"></i>Restablecer Contraseña
                        </h5>
                        <button type="button" class="close" wire:click="closePasswordModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="fas fa-user mr-1"></i><strong>{{ $passwordUserName }}</strong>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       id="autoGenerate" wire:model.live="autoGenerate">
                                <label class="custom-control-label" for="autoGenerate">
                                    Generar contraseña automáticamente
                                </label>
                            </div>
                        </div>
                        @if ($autoGenerate && $generatedPassword)
                            <div class="alert alert-info py-2">
                                <i class="fas fa-lock mr-1"></i>
                                <strong>Contraseña:</strong>
                                <code class="ml-1">{{ $generatedPassword }}</code>
                            </div>
                        @endif
                        @if (!$autoGenerate)
                            <div class="form-group">
                                <label>Nueva contraseña</label>
                                <input wire:model="newPassword" type="password"
                                       class="form-control @error('newPassword') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres">
                                @error('newPassword')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-0">
                                <label>Confirmar contraseña</label>
                                <input wire:model="newPasswordConfirmation" type="password"
                                       class="form-control" placeholder="Repetir contraseña">
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closePasswordModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="savePassword">
                            <i class="fas fa-key mr-1"></i>Restablecer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal eliminar --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-trash mr-2"></i>Eliminar Visitante
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
                            Esta acción es <strong>irreversible</strong>.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteUser">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal editar datos --}}
    @if ($showEditModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-edit mr-2"></i>Editar Datos del Visitante
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
                        <div class="form-group mb-0">
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
                            <i class="fas fa-save mr-1"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal crear visitante --}}
    @if ($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" style="font-size:1rem;">
                            <i class="fas fa-user-plus mr-2"></i>Crear Nuevo Visitante
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeCreateModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre completo <span class="text-danger">*</span></label>
                            <input wire:model="createName" type="text"
                                   class="form-control @error('createName') is-invalid @enderror"
                                   placeholder="Nombre completo">
                            @error('createName')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Correo electrónico <span class="text-danger">*</span></label>
                            <input wire:model="createEmail" type="email"
                                   class="form-control @error('createEmail') is-invalid @enderror"
                                   placeholder="correo@ejemplo.com">
                            @error('createEmail')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Departamento</label>
                            <select wire:model="createDepartamento" class="form-control">
                                <option value="">-- Seleccionar departamento --</option>
                                @foreach ($departamentos as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input"
                                       id="createAutoGenerate" wire:model.live="createAutoGenerate">
                                <label class="custom-control-label" for="createAutoGenerate">
                                    Generar contraseña automáticamente
                                </label>
                            </div>
                        </div>
                        @if ($createAutoGenerate && $createGeneratedPassword)
                            <div class="alert alert-info py-2">
                                <i class="fas fa-lock mr-1"></i>
                                <strong>Contraseña generada:</strong>
                                <code class="ml-1">{{ $createGeneratedPassword }}</code>
                                <small class="d-block mt-1 text-muted">Anote esta contraseña antes de guardar.</small>
                            </div>
                        @endif
                        @if (!$createAutoGenerate)
                            <div class="form-group">
                                <label>Contraseña <span class="text-danger">*</span></label>
                                <input wire:model="createPassword" type="password"
                                       class="form-control @error('createPassword') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres">
                                @error('createPassword')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Confirmar contraseña <span class="text-danger">*</span></label>
                                <input wire:model="createPasswordConfirmation" type="password"
                                       class="form-control" placeholder="Repetir contraseña">
                            </div>
                        @endif
                        <div class="alert alert-success py-2 mb-0">
                            <i class="fas fa-check-circle mr-1"></i>
                            El visitante quedará <strong>verificado y activo</strong> con rol <strong>Visitante</strong>.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-success" wire:click="createUser">
                            <i class="fas fa-user-plus mr-1"></i>Crear Visitante
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
