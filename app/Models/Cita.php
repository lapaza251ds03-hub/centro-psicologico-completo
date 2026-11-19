<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id', 'psicologo_id', 'fecha_hora', 'fecha_hora_fin',
        'modalidad', 'tipo_sesion', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora'     => 'datetime',
            'fecha_hora_fin' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function psicologo(): BelongsTo
    {
        return $this->belongsTo(Psicologo::class);
    }

    public function evaluacionClinica(): HasOne
    {
        return $this->hasOne(EvaluacionClinica::class);
    }
}
