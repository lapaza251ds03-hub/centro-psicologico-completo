<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psicologos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()
                  ->constrained()->nullOnDelete();
            $table->char('dni', 8)->unique();
            $table->string('nombres', 80);
            $table->string('apellidos', 80);
            $table->enum('especialidad', [
                'Clínica',
                'Infantil',
                'Pareja y Familia',
                'Neuropsicología',
                'Educativa',
            ]);
            $table->enum('grupo_etario', [
                'ninos',         // 0 - 11 años
                'adolescentes',  // 12 - 17 años
                'adultos',       // 18 - 59 años
                'adulto_mayor',  // 60+ años
            ]);
            $table->string('colegiatura', 15)->unique(); // N° C.Ps.P.
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psicologos');
    }
};
