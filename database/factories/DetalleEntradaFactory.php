<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Entrada;
use App\Models\Producto;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleEntrada>
 */
class DetalleEntradaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_entrada'   => Entrada::factory(),
            'id_producto'  => Producto::factory(),
            'cantidad'     => $this->faker->numberBetween(1, 50),
        ];
    }
}
