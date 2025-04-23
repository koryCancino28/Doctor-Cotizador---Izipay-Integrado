<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulacion extends Model
{
    use HasFactory;

    protected $fillable = ['item', 'name', 'precio_publico', 'precio_medico', 'cliente_id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'formulacion_id');
    }
}