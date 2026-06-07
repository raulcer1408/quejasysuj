<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuejaSeguimiento extends Model
{
    protected $fillable = [
        'queja_id',
        'user_id',
        'accion',
        'comentario',
        'estado_anterior',
        'estado_nuevo',
    ];

    public function queja(): BelongsTo
    {
        return $this->belongsTo(Queja::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
