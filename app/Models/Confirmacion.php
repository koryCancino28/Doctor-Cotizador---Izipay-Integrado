<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmacion extends Model
{
    use HasFactory;
    protected $table = 'confirmaciones';
    protected $fillable = [
        'cotizacion_id',
        'visitadora_id',
        'archivo',
        'tipo_archivo',
    ];

    /**
     * Relación con la cotización.
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Relación con la visitadora (usuario).
     */
    public function visitadora()
    {
        return $this->belongsTo(User::class, 'visitadora_id');
    }
}
