<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'cmp', 'tipo_delivery', 'user_id', 'telefono', 'direccion', 'visitadora_id'];
    
    // Relación con la visitadora médica (usuario con role_id=3)
    public function visitadora()
    {
        return $this->belongsTo(User::class, 'visitadora_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formulaciones()
    {
        return $this->hasMany(Formulacion::class, 'cliente_id');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'cliente_id');
    }
    
}
