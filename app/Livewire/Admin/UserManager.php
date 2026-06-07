<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';

    // Modal cambiar rol
    public bool $showModal = false;
    public ?int $editingUserId = null;
    public string $editingRole = '';
    public string $editingName = '';
    public string $editingUnidad = '';
    public bool $editingEsMiembroComision = false;

    // Modal resetear contraseña
    public bool $showPasswordModal = false;
    public ?int $passwordUserId = null;
    public string $passwordUserName = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';
    public bool $autoGenerate = false;
    public string $generatedPassword = '';

    // Modal editar datos
    public bool $showEditModal = false;
    public ?int $editDataUserId = null;
    public string $editDataName  = '';
    public string $editDataEmail = '';
    public string $editDataDepartamento = '';

    // Modal eliminar usuario
    public bool $showDeleteModal = false;
    public ?int $deleteUserId   = null;
    public string $deleteUserName = '';

    // Modal crear usuario
    public bool $showCreateModal = false;
    public string $createName     = '';
    public string $createEmail    = '';
    public string $createRole     = '';
    public string $createUnidad   = '';
    public string $createDepartamento = '';
    public string $createPassword = '';
    public string $createPasswordConfirmation = '';
    public bool   $createAutoGenerate  = false;
    public string $createGeneratedPassword = '';

    // Modal editar departamento
    public string $editingDepartamento = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    // — Modal rol —
    public function openModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingUserId              = $user->id;
        $this->editingName                = $user->name;
        $this->editingRole                = $user->role;
        $this->editingUnidad              = $user->unidad ?? '';
        $this->editingDepartamento        = $user->departamento ?? '';
        $this->editingEsMiembroComision   = (bool) $user->es_miembro_comision;
        $this->showModal                  = true;
    }

    public function closeModal(): void
    {
        $this->showModal                = false;
        $this->editingUserId            = null;
        $this->editingRole              = '';
        $this->editingName              = '';
        $this->editingUnidad            = '';
        $this->editingDepartamento      = '';
        $this->editingEsMiembroComision = false;
    }

    public function saveRole(): void
    {
        $rolesConUnidad = User::ROLES_CON_UNIDAD;

        $this->validate([
            'editingRole'   => ['required', 'in:' . implode(',', array_keys(User::roles()))],
            'editingUnidad' => [
                in_array($this->editingRole, $rolesConUnidad) ? 'required' : 'nullable',
                'in:' . implode(',', array_keys(User::UNIDADES)),
            ],
        ], [
            'editingUnidad.required' => 'La unidad es obligatoria para este rol.',
        ]);

        $target = User::findOrFail($this->editingUserId);
        $rolAnterior = User::roles()[$target->role] ?? $target->role;
        $rolNuevo    = User::roles()[$this->editingRole] ?? $this->editingRole;

        $target->update([
            'role'                => $this->editingRole,
            'unidad'              => in_array($this->editingRole, $rolesConUnidad) ? $this->editingUnidad : null,
            'departamento'        => $this->editingDepartamento ?: null,
            'es_miembro_comision' => $this->editingEsMiembroComision,
        ]);

        AuditLog::registrar('Cambió rol', "Usuario: {$target->name} ({$target->email}) — De: {$rolAnterior} → A: {$rolNuevo}");

        $this->closeModal();
        session()->flash('success', 'Rol actualizado correctamente.');
    }

    // — Modal contraseña —
    public function openPasswordModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->passwordUserId       = $user->id;
        $this->passwordUserName     = $user->name;
        $this->newPassword          = '';
        $this->newPasswordConfirmation = '';
        $this->autoGenerate         = false;
        $this->generatedPassword    = '';
        $this->showPasswordModal    = true;
    }

    public function closePasswordModal(): void
    {
        $this->showPasswordModal    = false;
        $this->passwordUserId       = null;
        $this->passwordUserName     = '';
        $this->newPassword          = '';
        $this->newPasswordConfirmation = '';
        $this->autoGenerate         = false;
        $this->generatedPassword    = '';
    }

    public function updatingAutoGenerate(bool $value): void
    {
        if ($value) {
            $this->generatedPassword = Str::password(12);
            $this->newPassword = $this->generatedPassword;
            $this->newPasswordConfirmation = $this->generatedPassword;
        } else {
            $this->generatedPassword = '';
            $this->newPassword = '';
            $this->newPasswordConfirmation = '';
        }
    }

    // — Modal eliminar usuario —
    // — Modal editar datos —
    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editDataUserId      = $user->id;
        $this->editDataName        = $user->name;
        $this->editDataEmail       = $user->email;
        $this->editDataDepartamento = $user->departamento ?? '';
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal       = false;
        $this->editDataUserId      = null;
        $this->editDataName        = '';
        $this->editDataEmail       = '';
        $this->editDataDepartamento = '';
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editDataName'  => ['required', 'string', 'max:255'],
            'editDataEmail' => ['required', 'email', 'max:255',
                                'unique:users,email,' . $this->editDataUserId],
        ], [
            'editDataName.required'  => 'El nombre es obligatorio.',
            'editDataEmail.required' => 'El correo es obligatorio.',
            'editDataEmail.email'    => 'Ingrese un correo válido.',
            'editDataEmail.unique'   => 'Este correo ya está registrado.',
        ]);

        $target = User::findOrFail($this->editDataUserId);
        $target->update([
            'name'         => $this->editDataName,
            'email'        => $this->editDataEmail,
            'departamento' => $this->editDataDepartamento ?: null,
        ]);

        AuditLog::registrar('Editó usuario', "Datos actualizados: {$target->name} ({$target->email})");

        $this->closeEditModal();
        session()->flash('success', 'Datos del usuario actualizados correctamente.');
    }

    public function openDeleteModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->deleteUserId   = $user->id;
        $this->deleteUserName = $user->name;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteUserId   = null;
        $this->deleteUserName = '';
    }

    public function deleteUser(): void
    {
        $user = User::findOrFail($this->deleteUserId);

        // No permitir eliminar al propio usuario autenticado
        if ($user->id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            $this->closeDeleteModal();
            return;
        }

        $nombre = $user->name;
        $email  = $user->email;
        $user->delete();

        AuditLog::registrar('Eliminó usuario', "Usuario eliminado: {$nombre} ({$email})");

        $this->closeDeleteModal();
        session()->flash('success', "Usuario «{$nombre}» eliminado correctamente.");
    }

    // — Modal crear usuario —
    public function openCreateModal(): void
    {
        $this->createName                 = '';
        $this->createEmail                = '';
        $this->createRole                 = '';
        $this->createUnidad               = '';
        $this->createDepartamento         = '';
        $this->createPassword             = '';
        $this->createPasswordConfirmation = '';
        $this->createAutoGenerate         = false;
        $this->createGeneratedPassword    = '';
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal            = false;
        $this->createName                 = '';
        $this->createEmail                = '';
        $this->createRole                 = '';
        $this->createUnidad               = '';
        $this->createDepartamento         = '';
        $this->createPassword             = '';
        $this->createPasswordConfirmation = '';
        $this->createAutoGenerate         = false;
        $this->createGeneratedPassword    = '';
    }

    public function updatingCreateAutoGenerate(bool $value): void
    {
        if ($value) {
            $this->createGeneratedPassword    = Str::password(12);
            $this->createPassword             = $this->createGeneratedPassword;
            $this->createPasswordConfirmation = $this->createGeneratedPassword;
        } else {
            $this->createGeneratedPassword    = '';
            $this->createPassword             = '';
            $this->createPasswordConfirmation = '';
        }
    }

    public function createUser(): void
    {
        $rolesConUnidad = User::ROLES_CON_UNIDAD;

        $this->validate([
            'createName'     => ['required', 'string', 'max:255'],
            'createEmail'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'createRole'     => ['required', 'in:' . implode(',', array_keys(User::roles()))],
            'createUnidad'   => [
                in_array($this->createRole, $rolesConUnidad) ? 'required' : 'nullable',
                'in:' . implode(',', array_keys(User::UNIDADES)),
            ],
            'createPassword' => ['required', 'min:8', 'same:createPasswordConfirmation'],
        ], [
            'createName.required'     => 'El nombre es obligatorio.',
            'createEmail.required'    => 'El correo es obligatorio.',
            'createEmail.email'       => 'Ingrese un correo válido.',
            'createEmail.unique'      => 'Este correo ya está registrado.',
            'createRole.required'     => 'Debe seleccionar un rol.',
            'createUnidad.required'   => 'La unidad es obligatoria para este rol.',
            'createPassword.required' => 'La contraseña es obligatoria.',
            'createPassword.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'createPassword.same'     => 'Las contraseñas no coinciden.',
        ]);

        $user = User::create([
            'name'         => $this->createName,
            'email'        => $this->createEmail,
            'password'     => Hash::make($this->createPassword),
            'role'         => $this->createRole,
            'unidad'       => in_array($this->createRole, $rolesConUnidad) ? $this->createUnidad : null,
            'departamento' => $this->createDepartamento ?: null,
            'activo'       => true,
        ]);

        // Marcar correo como verificado sin requerir confirmación
        $user->markEmailAsVerified();

        $rolLabel = User::roles()[$user->role] ?? $user->role;
        AuditLog::registrar('Creó usuario', "Nuevo usuario: {$user->name} ({$user->email}) — Rol: {$rolLabel}");

        $this->closeCreateModal();
        session()->flash('success', "Usuario «{$user->name}» creado correctamente.");
    }

    public function toggleActivo(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['activo' => !$user->activo]);

        $estado  = $user->activo ? 'activado' : 'desactivado';
        $accion  = $user->activo ? 'Activó usuario' : 'Desactivó usuario';
        AuditLog::registrar($accion, "Usuario {$estado}: {$user->name} ({$user->email})");

        session()->flash('success', "Usuario {$estado} correctamente.");
    }

    public function savePassword(): void
    {
        $this->validate([
            'newPassword' => ['required', 'min:8', 'same:newPasswordConfirmation'],
        ], [
            'newPassword.required' => 'La contraseña es obligatoria.',
            'newPassword.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'newPassword.same'     => 'Las contraseñas no coinciden.',
        ]);

        $target = User::findOrFail($this->passwordUserId);
        $target->update(['password' => Hash::make($this->newPassword)]);

        AuditLog::registrar('Restableció contraseña', "Contraseña restablecida para: {$target->name} ({$target->email})");

        $this->closePasswordModal();
        session()->flash('success', 'Contraseña restablecida correctamente.');
    }

    public function render()
    {
        $usuarios = User::query()
            ->where('role', '!=', User::ROLE_VISITANTE)
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->when($this->filterRole, fn($q) =>
                $q->where('role', $this->filterRole)
            )
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.user-manager', [
            'usuarios'       => $usuarios,
            'roles'          => User::roles(),
            'unidades'       => User::UNIDADES,
            'rolesConUnidad' => User::ROLES_CON_UNIDAD,
            'departamentos'  => User::DEPARTAMENTOS,
        ]);
    }
}
