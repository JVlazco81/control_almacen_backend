<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usuario;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistorialCambio>
 */
class HistorialCambioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_auditado'   => $this->faker->randomElement(['Producto','Entrada','Salida']),
            'id_auditado'     => 1, // Ajusta en cada test al id correcto
            'id_usuario'      => Usuario::factory(),
            'accion'          => $this->faker->randomElement(['insercion','actualizacion','eliminacion']),
            'valor_anterior'  => null,
            'valor_nuevo'     => json_encode(['foo' => 'bar']),
            'fecha'           => $this->faker->dateTime(),
        ];
    }
}
