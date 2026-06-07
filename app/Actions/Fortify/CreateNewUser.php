<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'departamento' => ['required', Rule::in(array_keys(User::DEPARTAMENTOS))],
            'password'     => $this->passwordRules(),
            'terms'        => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'departamento.required' => 'Seleccione su departamento.',
            'departamento.in'       => 'El departamento seleccionado no es válido.',
        ])->validate();

        return User::create([
            'name'         => $input['name'],
            'email'        => $input['email'],
            'departamento' => $input['departamento'],
            'password'     => Hash::make($input['password']),
        ]);
    }
}
