<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('dni', 8)->unique()->nullable()->after('id');
            $table->enum('rol', ['admin', 'psicologo', 'recepcionista'])
                  ->default('recepcionista')
                  ->after('email');
            $table->enum('estado', ['pendiente', 'activo', 'inactivo'])
                  ->default('pendiente')
                  ->after('rol');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dni', 'rol', 'estado']);
        });
    }
};
