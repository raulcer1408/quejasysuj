<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfileModal extends Component
{
    public bool $show = false;
    public string $tab = 'datos';

    public string $name = '';
    public string $email = '';

    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    #[On('openProfileModal')]
    public function open(): void
    {
        $user = Auth::user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->tab   = 'datos';
        $this->resetValidation();
        $this->currentPassword         = '';
        $this->newPassword             = '';
        $this->newPasswordConfirmation = '';
        $this->show = true;
    }

    public function close(): void
    {
        $this->show                    = false;
        $this->currentPassword         = '';
        $this->newPassword             = '';
        $this->newPasswordConfirmation = '';
    }

    public function guardarDatos(): void
    {
        $user = Auth::user();

        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'Ingrese un correo válido.',
            'email.unique'   => 'Este correo ya está registrado.',
        ]);

        $user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        $this->close();
        $this->dispatch('profileUpdated', message: 'Datos actualizados correctamente.');
    }

    public function guardarPassword(): void
    {
        $this->validate([
            'currentPassword' => ['required'],
            'newPassword'     => ['required', 'min:8', 'same:newPasswordConfirmation'],
        ], [
            'currentPassword.required' => 'Ingrese su contraseña actual.',
            'newPassword.required'     => 'La nueva contraseña es obligatoria.',
            'newPassword.min'          => 'Mínimo 8 caracteres.',
            'newPassword.same'         => 'Las contraseñas no coinciden.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'La contraseña actual es incorrecta.');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->close();
        $this->dispatch('profileUpdated', message: 'Contraseña actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.profile-modal');
    }
}
