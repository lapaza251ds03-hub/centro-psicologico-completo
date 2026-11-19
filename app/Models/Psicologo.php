<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Psicologo extends Model
{
    use HasFactory;

    protected $table = 'psicologos';

    protected $fillable = [
        'user_id', 'dni', 'nombres', 'apellidos',
        'especialidad', 'grupo_etario', 'colegiatura', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function nombreCompleto(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function etiquetaGrupoEtario(): string
    {
        return match ($this->grupo_etario) {
            'ninos' => 'Niños (0–11 años)',
            'adolescentes' => 'Adolescentes (12–17 años)',
            'adultos' => 'Adultos (18–59 años)',
            'adulto_mayor' => 'Adulto Mayor (60+ años)',
        };
    }
}
