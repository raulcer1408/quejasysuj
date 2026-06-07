<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    const ROLE_SUPERUSUARIO        = 'superusuario';
    const ROLE_SISTEMAS            = 'sistemas';
    const ROLE_JEFE_UNIDAD         = 'jefe_unidad'; // legacy
    const ROLE_JEFE_FORMACION      = 'jefe_formacion';
    const ROLE_JEFE_CAPACITACION   = 'jefe_capacitacion';
    const ROLE_JEFE_ADMINISTRATIVO = 'jefe_administrativo';
    const ROLE_PEDAGOGA              = 'pedagoga';              // legacy
    const ROLE_PEDAGOGA_FORMACION    = 'pedagoga_formacion';
    const ROLE_PEDAGOGA_CAPACITACION = 'pedagoga_capacitacion';
    const ROLE_MIEMBRO_COMISION      = 'miembro_comision';      // legacy
    const ROLE_RESPONSABLE_REVISTA   = 'responsable_revista';
    const ROLE_REVISOR_ADMIN         = 'revisor_administrativo'; // legacy
    const ROLE_RESPONSABLE_ADMIN     = 'responsable_administrativo';

    const ROLES_REVISORES = [
        self::ROLE_PEDAGOGA_FORMACION,
        self::ROLE_PEDAGOGA_CAPACITACION,
        self::ROLE_RESPONSABLE_REVISTA,
        self::ROLE_RESPONSABLE_ADMIN,
        self::ROLE_PEDAGOGA,          // legacy
        self::ROLE_MIEMBRO_COMISION,  // legacy
        self::ROLE_REVISOR_ADMIN,     // legacy
    ];
    const ROLE_COORDINADOR         = 'coordinador';                        // legacy
    const ROLE_COORDINADOR_ACADEMICO = 'coordinador_academico_capacitacion';
    const ROLE_DOCENTE          = 'docente';          // legacy
    const ROLE_DOCENTE_INTERNO  = 'docente_interno';
    const ROLE_VISITANTE           = 'visitante';
    const ROLE_USUARIO_EJE         = 'usuario_eje';

    const ROLES_JEFE = [
        self::ROLE_JEFE_FORMACION,
        self::ROLE_JEFE_CAPACITACION,
        self::ROLE_JEFE_ADMINISTRATIVO,
        self::ROLE_JEFE_UNIDAD, // backward compat
    ];

    const DEPARTAMENTOS = [
        'la_paz'      => 'La Paz',
        'cochabamba'  => 'Cochabamba',
        'santa_cruz'  => 'Santa Cruz',
        'oruro'       => 'Oruro',
        'potosi'      => 'Potosí',
        'chuquisaca'  => 'Chuquisaca',
        'tarija'      => 'Tarija',
        'beni'        => 'Beni',
        'pando'       => 'Pando',
    ];

    const UNIDADES = [
        'formacion'      => 'Formación',
        'capacitacion'   => 'Capacitación',
        'administrativo' => 'Administrativo',
        'investigacion'  => 'Investigación (Revista)',
    ];

    const ROLES_CON_UNIDAD = [
        self::ROLE_PEDAGOGA,
        self::ROLE_MIEMBRO_COMISION,
        self::ROLE_REVISOR_ADMIN,
    ];

    public static function roles(): array
    {
        return [
            self::ROLE_SUPERUSUARIO        => 'Superusuario',
            self::ROLE_SISTEMAS            => 'Encargado de Sistemas',
            self::ROLE_JEFE_FORMACION      => 'Jefe de Formación',
            self::ROLE_JEFE_CAPACITACION   => 'Jefe de Capacitación',
            self::ROLE_JEFE_ADMINISTRATIVO => 'Jefe Unidad Administrativa',
            self::ROLE_PEDAGOGA_FORMACION    => 'Pedagoga de Formación',
            self::ROLE_PEDAGOGA_CAPACITACION => 'Pedagoga de Capacitación',
            self::ROLE_RESPONSABLE_REVISTA   => 'Responsable de Revista',
            self::ROLE_RESPONSABLE_ADMIN     => 'Responsable Administrativo',
            self::ROLE_COORDINADOR_ACADEMICO => 'Coordinador Académico de Capacitación',
            self::ROLE_DOCENTE_INTERNO  => 'Docente Interno',
            self::ROLE_VISITANTE           => 'Visitante',
            self::ROLE_USUARIO_EJE         => 'Usuario EJE',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function requiereUnidad(): bool
    {
        return in_array($this->role, self::ROLES_CON_UNIDAD);
    }

    public function isSuperusuario(): bool      { return $this->role === self::ROLE_SUPERUSUARIO; }
    public function isSistemas(): bool          { return $this->role === self::ROLE_SISTEMAS; }
    public function isJefeUnidad(): bool        { return in_array($this->role, self::ROLES_JEFE); }
    public function isPedagoga(): bool          { return $this->role === self::ROLE_PEDAGOGA; }
    public function isMiembroComision(): bool   { return (bool) $this->es_miembro_comision || $this->role === self::ROLE_MIEMBRO_COMISION; }
    public function isRevisorAdmin(): bool      { return $this->role === self::ROLE_REVISOR_ADMIN; }
    public function isCoordinador(): bool       { return in_array($this->role, [self::ROLE_COORDINADOR_ACADEMICO, self::ROLE_COORDINADOR]); }
    public function isDocente(): bool           { return in_array($this->role, [self::ROLE_DOCENTE_INTERNO, self::ROLE_DOCENTE]); }
    public function isVisitante(): bool         { return $this->role === self::ROLE_VISITANTE; }
    public function isUsuarioEje(): bool        { return $this->role === self::ROLE_USUARIO_EJE; }
    public function isRevisor(): bool           { return in_array($this->role, self::ROLES_REVISORES) || $this->es_miembro_comision; }

    public function scopeRevisoresParaServicio(Builder $query, string $rolRevisor): Builder
    {
        if ($rolRevisor === self::ROLE_RESPONSABLE_REVISTA) {
            return $query->where('activo', true)->where('es_miembro_comision', true);
        }

        return $query->where('activo', true)->where('role', $rolRevisor);
    }


    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'unidad',
        'activo',
        'departamento',
        'es_miembro_comision',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'activo'              => 'boolean',
            'es_miembro_comision' => 'boolean',
        ];
    }
    public function adminlte_profile_url(){
        return url('user/profile');
    }
}
