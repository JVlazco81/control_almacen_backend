<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // FK a roles.id_rol
            'id_rol'           => Rol::factory(),

            // Nombres y apellidos según longitudes de migración
            'primer_nombre'    => $this->faker->firstName(),
            'segundo_nombre'   => $this->faker->optional()->firstName(),
            'primer_apellido'  => $this->faker->lastName(),
            'segundo_apellido' => $this->faker->lastName(),

            // Contraseña hasheada; longitud 255
            'usuario_password' => Hash::make('password'),
        ];
    }
}
