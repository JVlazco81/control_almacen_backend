<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Salida;
use App\Models\Producto;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleSalida>
 */
class DetalleSalidaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_salida'    => Salida::factory(),
            'id_producto'  => Producto::factory(),
            'cantidad'     => $this->faker->numberBetween(1, 50),
        ];
    }
}
