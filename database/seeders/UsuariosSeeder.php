<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'id_rol'           => 2, // director
                'primer_nombre'    => 'Juan',
                'segundo_nombre'   => 'Carlos',      
                'primer_apellido'  => 'Lozano',
                'segundo_apellido' => 'Vergara',
                'usuario_password' => Hash::make('Pass_123'),
            ],
            [
                'id_rol'           => 1, // almacenista
                'primer_nombre'    => 'Juan',
                'segundo_nombre'   => 'Fernando',
                'primer_apellido'  => 'Chuc',
                'segundo_apellido' => 'Conkle',
                'usuario_password' => Hash::make('Pass_456'),
            ],
        ]);
    }
}
