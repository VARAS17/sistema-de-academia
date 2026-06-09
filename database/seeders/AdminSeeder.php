<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
 public function run(): void
    {
        // Creamos el usuario administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin@academia.com'], // Busca por email
            [
                'name'     => 'Administrador Sistema',
                'password' => Hash::make('admin123'), // Cambia esta clave en producción
            ]
        );

        // Le asignamos el rol de admin (asegúrate que RoleSeeder corra antes)
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
        
        $this->command->info('Usuario Admin creado con éxito: admin@academia.com');
    }
}
