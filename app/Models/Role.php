<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Especificar los atributos que pueden ser asignados masivamente
    protected $fillable = ['name', 'description'];
    public function users()
    {
        return $this->hasMany(User::class); // Un post tiene muchos comentarios
    }
}
