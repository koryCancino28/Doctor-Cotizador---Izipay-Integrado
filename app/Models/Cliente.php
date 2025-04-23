<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'cmp', 'tipo_delivery', 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
