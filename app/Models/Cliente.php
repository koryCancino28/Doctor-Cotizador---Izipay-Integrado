<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'cmp', 'tipo_delivery', 'user_id', 'telefono', 'direccion'];
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
