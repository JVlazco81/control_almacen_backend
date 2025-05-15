<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proveedor;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entrada>
 */
class EntradaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // FK a proveedores.id_proveedor
            'id_proveedor'  => Proveedor::factory(),

            'folio'         => $this->faker->bothify('F-###'),
            'entrada_anual' => $this->faker->numberBetween(1, 100),
            'fecha_factura' => $this->faker->date(),
            'fecha_entrada' => $this->faker->date(),
            'nota'          => $this->faker->optional()->sentence(),
        ];
    }
}
