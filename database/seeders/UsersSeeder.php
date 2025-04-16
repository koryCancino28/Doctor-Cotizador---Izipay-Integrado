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
            'email' => 'sistemas@grobdi.com',
            'password' => Hash::make('12345678'), // Usando bcrypt para encriptar la contraseña
            'role_id' => 1,  // Asignar el rol de Admin (ID = 1)
        ]);
    }
}
