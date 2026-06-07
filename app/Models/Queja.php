<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Queja extends Model
{
    const ESTADOS = [
        'pendiente'            => 'Pendiente',
        'en_revision'          => 'En Revisión',
        'derivado_coordinador' => 'Derivado a Coordinador',
        'respondido'           => 'Respondido',
        'en_validacion'        => 'En Validación',
        'resuelto'             => 'Resuelto',
        'no_procede'           => 'No Procede',
    ];

    const COLORES_ESTADO = [
        'pendiente'            => 'warning',
        'en_revision'          => 'info',
        'derivado_coordinador' => 'primary',
        'respondido'           => 'secondary',
        'en_validacion'        => 'purple',
        'resuelto'             => 'success',
        'no_procede'           => 'danger',
    ];

    const SERVICIOS = [
        'formacion'      => 'Formación',
        'capacitacion'   => 'Capacitación',
        'administrativo' => 'Administrativo',
        'investigacion'  => 'Investigación (Revista)',
    ];

    const TIPOS = [
        'queja'      => 'Queja',
        'sugerencia' => 'Sugerencia de Mejora',
    ];

    const REVISOR_POR_SERVICIO = [
        'formacion'      => User::ROLE_PEDAGOGA_FORMACION,
        'capacitacion'   => User::ROLE_PEDAGOGA_CAPACITACION,
        'administrativo' => User::ROLE_JEFE_ADMINISTRATIVO,
        'investigacion'  => User::ROLE_RESPONSABLE_REVISTA,
    ];

    // Servicios que se asignan directamente al jefe sin pasar por revisor
    const SERVICIOS_DIRECTOS_JEFE = ['administrativo'];

    protected $fillable = [
        'user_id',
        'revisor_id',
        'coordinador_id',
        'jefe_id',
        'tipo_solicitud',
        'servicio',
        'nombre_actividad',
        'descripcion',
        'aceptacion',
        'respaldo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'aceptacion' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }

    public function coordinador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinador_id');
    }

    public function jefe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jefe_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(QuejaSeguimiento::class);
    }

    public function registrarSeguimiento(User $user, string $accion, string $estadoNuevo, ?string $comentario = null): void
    {
        $this->seguimientos()->create([
            'user_id'         => $user->id,
            'accion'          => $accion,
            'comentario'      => $comentario,
            'estado_anterior' => $this->estado,
            'estado_nuevo'    => $estadoNuevo,
        ]);

        $this->update(['estado' => $estadoNuevo]);
    }

    public function etiquetaEstado(): string
    {
        if ($this->estado === 'derivado_coordinador' && $this->relationLoaded('coordinador') && $this->coordinador) {
            $rolLabel = User::roles()[$this->coordinador->role] ?? ucfirst($this->coordinador->role);
            return "Derivado a {$rolLabel}";
        }

        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function colorEstado(): string
    {
        return self::COLORES_ESTADO[$this->estado] ?? 'secondary';
    }

    public function etiquetaAceptacion(): ?string
    {
        return match ($this->estado) {
            'resuelto'   => 'Aceptada',
            'no_procede' => 'Rechazada',
            default      => null,
        };
    }

    public function colorAceptacion(): string
    {
        return match ($this->estado) {
            'resuelto'   => 'success',
            'no_procede' => 'danger',
            default      => 'secondary',
        };
    }
}
