<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Unidad;
use App\Models\Categoria;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // La migración define `codigo` como FK a `categorias.codigo`
            // Creamos primero una categoría y reutilizamos su código
            'codigo'               => Categoria::factory(),

            // descripción de hasta 150 caracteres
            'descripcion_producto' => $this->faker->text(100),

            // marca opcional, hasta 100 caracteres
            'marca'                => $this->faker->optional()->company,

            // cantidad entera, default 0 en migración
            'cantidad'             => $this->faker->numberBetween(0, 100),

            // FK a unidades.id_unidad
            'id_unidad'            => Unidad::factory(),

            // precio decimal (10,2)
            'precio'               => $this->faker->randomFloat(2, 1, 1000),
            
            // deleted_at lo gestiona el soft delete; no se incluye aquí
        ];
    }
}
