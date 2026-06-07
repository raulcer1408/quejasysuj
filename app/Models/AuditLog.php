<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'accion', 'descripcion', 'ip'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar(string $accion, string $descripcion): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'ip'          => request()->ip(),
        ]);
    }
}
