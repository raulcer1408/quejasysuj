<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quejas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('revisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coordinador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo_solicitud', ['queja', 'sugerencia']);
            $table->enum('servicio', ['formacion', 'capacitacion', 'administrativo', 'investigacion']);
            $table->string('nombre_actividad');
            $table->text('descripcion');
            $table->boolean('aceptacion')->default(false);
            $table->string('respaldo')->nullable();
            $table->enum('estado', [
                'pendiente',
                'en_revision',
                'derivado_coordinador',
                'respondido',
                'en_validacion',
                'resuelto',
            ])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quejas');
    }
};
