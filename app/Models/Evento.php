<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asistencia;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'ponente_id',
        'capacidad_maxima',
    ];

    /**
     * Relación con Ponente (Un evento tiene un ponente).
     */
    public function ponente()
    {
        return $this->belongsTo(Ponente::class);
    }

    /**
     * Relación con Asistencias (Un evento puede tener muchos asistentes).
     */
    public function asistentes()
    {
        return $this->belongsToMany(User::class, 'asistencias')
            ->withTimestamps();
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'evento_id');
    }
}
