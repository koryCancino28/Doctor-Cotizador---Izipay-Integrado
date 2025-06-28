<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear un usuario
        User::create([
            'name' => 'Sistemas',
            'last_name' => 'Grobdi',
            'email' => 'sistemas@grobdi.com',
            'password' => Hash::make('12345678'), // Usando bcrypt para encriptar la contraseña
            'role_id' => 1,  
        ]);
        User::create([
            'name' => 'Cristopher',
            'last_name' => 'Alcantara',
            'email' => 'jefe.proyectos@grobdi.com',
            'password' => Hash::make('12345678'), // Usando bcrypt para encriptar la contraseña
            'role_id' => 2,  
        ]);
    }
}
