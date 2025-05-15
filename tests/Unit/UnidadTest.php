<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Unidad;
use App\Models\Producto;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class UnidadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unidad_tiene_productos()
    {
        // Creamos una unidad
        $unidad = Unidad::factory()->create(['tipo_unidad' => 'PZA']);

        // Creamos 2 productos referenciando esa unidad
        $productos = Producto::factory()
            ->count(2)
            ->create(['id_unidad' => $unidad->id_unidad]);

        $unidad->refresh();

        $this->assertCount(2, $unidad->productos);
        $this->assertTrue($unidad->productos->contains($productos[0]));
        $this->assertTrue($unidad->productos->contains($productos[1]));
    }

    /** @test */
    public function tipo_unidad_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Unidad::factory()->create([
            'tipo_unidad' => null,
        ]);
    }

    /** @test */
    public function tipo_unidad_no_puede_superar_20_caracteres()
    {
        $this->expectException(QueryException::class);
        Unidad::factory()->create([
            'tipo_unidad' => Str::repeat('X', 21),
        ]);
    }
}
