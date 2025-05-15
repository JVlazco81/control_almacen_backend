<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Models\Proveedor;
use App\Models\Entrada;

class ProveedorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function proveedor_tiene_entradas()
    {
        $prov = Proveedor::factory()->create();

        $entradas = Entrada::factory()
            ->count(3)
            ->create(['id_proveedor' => $prov->id_proveedor]);

        $prov->refresh();

        $this->assertCount(3, $prov->entradas);
        $this->assertTrue($prov->entradas->contains($entradas[0]));
    }

    /** @test */
    public function nombre_proveedor_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Proveedor::factory()->create(['nombre_proveedor' => null]);
    }

    /** @test */
    public function nombre_proveedor_no_puede_superar_100_caracteres()
    {
        $this->expectException(QueryException::class);
        Proveedor::factory()->create([
            'nombre_proveedor' => Str::repeat('X', 101),
        ]);
    }
}
