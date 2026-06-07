<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('es_miembro_comision')->default(false)->after('departamento');
            $table->boolean('es_encargado_revision')->default(false)->after('es_miembro_comision');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['es_miembro_comision', 'es_encargado_revision']);
        });
    }
};
