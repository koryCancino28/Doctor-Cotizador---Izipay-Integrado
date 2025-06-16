<?php

// App\Models\DetalleCotizacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    protected $table = 'detalle_cotizacion';

    protected $fillable = [
        'cotizacion_id',
        'formulacion_id',
        'cantidad',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function formulacion()
    {
        return $this->belongsTo(Formulacion::class);
    }
}
