<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE quejas MODIFY COLUMN estado ENUM(
            'pendiente',
            'en_revision',
            'derivado_coordinador',
            'respondido',
            'en_validacion',
            'resuelto',
            'no_procede'
        ) NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quejas MODIFY COLUMN estado ENUM(
            'pendiente',
            'en_revision',
            'derivado_coordinador',
            'respondido',
            'en_validacion',
            'resuelto'
        ) NOT NULL DEFAULT 'pendiente'");
    }
};
