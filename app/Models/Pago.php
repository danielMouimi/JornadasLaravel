<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'monto',
        'estado',
        'paypal_transaction_id',
    ];

    /**
     * Relación con Usuario (Cada pago pertenece a un usuario).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
