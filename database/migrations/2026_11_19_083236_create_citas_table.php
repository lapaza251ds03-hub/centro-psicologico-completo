<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->restrictOnDelete();
            $table->foreignId('psicologo_id')->constrained('psicologos')->restrictOnDelete();
            $table->dateTime('fecha_hora');
            $table->dateTime('fecha_hora_fin'); // Bloque institucional de +60 minutos
            $table->enum('modalidad', ['presencial', 'virtual'])->default('presencial');
            $table->string('tipo_sesion', 80);
            $table->enum('estado', ['pendiente', 'confirmada', 'atendida', 'cancelada'])
                  ->default('pendiente');
            $table->timestamps();

            $table->index(['psicologo_id', 'fecha_hora']);
            $table->index(['paciente_id', 'fecha_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
