<div>
    {{-- Toast de éxito --}}
    <div id="profile-toast"
         style="display:none;position:fixed;top:20px;right:20px;z-index:9999;min-width:280px;"
         class="alert alert-success shadow">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="profile-toast-msg"></span>
    </div>

    @if ($show)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);z-index:1055;">
            <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-circle mr-2"></i>Mi Perfil
                        </h5>
                        <button type="button" class="close text-white" wire:click="close">
                            <span>&times;</span>
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <div class="modal-body p-0">
                        <ul class="nav nav-tabs nav-fill border-bottom-0 px-3 pt-3">
                            <li class="nav-item">
                                <button class="nav-link {{ $tab === 'datos' ? 'active' : '' }}"
                                        wire:click="$set('tab', 'datos')">
                                    <i class="fas fa-id-card mr-1"></i>Mis Datos
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $tab === 'password' ? 'active' : '' }}"
                                        wire:click="$set('tab', 'password')">
                                    <i class="fas fa-lock mr-1"></i>Contraseña
                                </button>
                            </li>
                        </ul>

                        <div class="p-4">

                            {{-- Tab: Datos personales --}}
                            @if ($tab === 'datos')
                                <div class="form-group">
                                    <label>Nombre completo <span class="text-danger">*</span></label>
                                    <input wire:model="name"
                                           type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="Nombre completo">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-0">
                                    <label>Correo electrónico <span class="text-danger">*</span></label>
                                    <input wire:model="email"
                                           type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder="correo@ejemplo.com">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            {{-- Tab: Contraseña --}}
                            @if ($tab === 'password')
                                <div class="form-group">
                                    <label>Contraseña actual <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input wire:model="currentPassword"
                                               type="password" id="pm-current"
                                               class="form-control @error('currentPassword') is-invalid @enderror"
                                               placeholder="Contraseña actual">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    onclick="togglePwd('pm-current', this)" tabindex="-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('currentPassword')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Nueva contraseña <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input wire:model="newPassword"
                                               type="password" id="pm-new"
                                               class="form-control @error('newPassword') is-invalid @enderror"
                                               placeholder="Mínimo 8 caracteres">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    onclick="togglePwd('pm-new', this)" tabindex="-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('newPassword')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label>Confirmar nueva contraseña <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input wire:model="newPasswordConfirmation"
                                               type="password" id="pm-confirm"
                                               class="form-control"
                                               placeholder="Repetir contraseña">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    onclick="togglePwd('pm-confirm', this)" tabindex="-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="close">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </button>
                        @if ($tab === 'datos')
                            <button type="button" class="btn btn-primary" wire:click="guardarDatos">
                                <i class="fas fa-save mr-1"></i>Guardar Datos
                            </button>
                        @else
                            <button type="button" class="btn btn-warning" wire:click="guardarPassword">
                                <i class="fas fa-key mr-1"></i>Actualizar Contraseña
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('profileUpdated', ({ message }) => {
        const toast = document.getElementById('profile-toast');
        document.getElementById('profile-toast-msg').textContent = message;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3500);
    });

    window.togglePwd = function (id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };
</script>
@endscript
