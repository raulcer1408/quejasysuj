<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quejas', function (Blueprint $table) {
            $table->foreignId('jefe_id')->nullable()->constrained('users')->nullOnDelete()->after('coordinador_id');
        });
    }

    public function down(): void
    {
        Schema::table('quejas', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'jefe_id');
            $table->dropColumn('jefe_id');
        });
    }
};
