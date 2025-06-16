<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = ['cliente_id', 'formulacion_id', 'cantidad', 'total', 'observacion', 'pdf_filename'];

    // App\Models\Cotizacion.php

    public function detalles()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    } 
}
