<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionClinica extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_clinicas';

    protected $fillable = [
        'cita_id', 'diagnostico_presuntivo', 'evolucion_terapeutica',
        'pruebas_aplicadas', 'observaciones_confidenciales',
    ];

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }
}
