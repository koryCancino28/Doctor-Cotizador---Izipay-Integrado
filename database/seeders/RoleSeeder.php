<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;  // Asegúrate de importar el modelo Role

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar roles en la tabla 'roles' usando el modelo Role
        Role::create([
            'name' => 'Admin',
            'description' => 'Administrador con todos los permisos',
        ]);
        Role::create([
            'name' => 'Jefe Proyecto',
            'description' => 'Usuario con permisos ilimitados',
        ]);
        Role::create([
            'name' => 'Visitadora Medica',
            'description' => 'Usuario que visualiza las proformas',
        ]);
        Role::create([
            'name' => 'Doctor',
            'description' => 'Doctor con permisos para gestionar sus formulaciones',
        ]);
        Role::create([
            'name' => 'Jefa Comercial',
            'description' => 'Usuario que visualiza los reportes de cada visitadora',
        ]);
    }
}
