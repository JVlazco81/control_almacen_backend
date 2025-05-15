<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function producto_pertenece_a_categoria()
    {
        // 1) Creamos una categoría con código específico
        $categoria = Categoria::factory()->create([
            'codigo'               => 123,
            'descripcion_categoria'=> 'TestCat',
        ]);

        // 2) Creamos el producto apuntando a esa categoría
        $producto = Producto::factory()->create([
            'codigo' => $categoria->codigo,
        ]);

        // 3) Aserciones
        $this->assertInstanceOf(Categoria::class, $producto->categoria);
        $this->assertEquals('TestCat', $producto->categoria->descripcion_categoria);
    }

    /** @test */
    public function producto_pertenece_a_unidad()
    {
        // 1) Creamos una unidad con tipo definido
        $unidad = Unidad::factory()->create([
            'tipo_unidad' => 'Caja',
        ]);

        // 2) Creamos el producto usando esa unidad
        $producto = Producto::factory()->create([
            'id_unidad' => $unidad->id_unidad,
        ]);

        // 3) Aserciones
        $this->assertInstanceOf(Unidad::class, $producto->unidad);
        $this->assertEquals('Caja', $producto->unidad->tipo_unidad);
    }
}
