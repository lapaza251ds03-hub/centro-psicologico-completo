<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni', 'nombres', 'apellidos', 'fecha_nacimiento', 'sexo',
        'telefono', 'motivo_consulta', 'apoderado_nombre',
        'apoderado_dni', 'consentimiento_informado', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'consentimiento_informado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function evaluacionesClinicas(): HasManyThrough
    {
        return $this->hasManyThrough(EvaluacionClinica::class, Cita::class);
    }

    public function nombreCompleto(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function esMenorDeEdad(): bool
    {
        return $this->fecha_nacimiento->age < 18;
    }

    // Determina el grupo etario exacto según RN05
    public function grupoEtario(): string
    {
        $edad = $this->fecha_nacimiento->age;

        return match (true) {
            $edad <= 11 => 'ninos',
            $edad <= 17 => 'adolescentes',
            $edad <= 59 => 'adultos',
            default     => 'adulto_mayor',
        };
    }
}
