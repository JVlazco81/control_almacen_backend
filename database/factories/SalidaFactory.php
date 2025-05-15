<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Departamento;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salida>
 */
class SalidaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // FK a departamentos.id_departamento
            'id_departamento' => Departamento::factory(),

            'folio'         => $this->faker->bothify('S-###'),
            'salida_anual'  => $this->faker->numberBetween(1, 100),
            'fecha_salida'  => $this->faker->date(),
            'orden_compra'  => $this->faker->numberBetween(1000, 9999),
        ];
    }
}
