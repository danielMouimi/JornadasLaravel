<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ponente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'foto',
        'experiencia',
        'redes_sociales',
    ];

    /**
     * Relación con Eventos (Un ponente puede tener varios eventos).
     */
    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

}
