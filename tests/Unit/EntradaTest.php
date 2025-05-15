<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Entrada;
use App\Models\Proveedor;
use App\Models\DetalleEntrada;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;

class EntradaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function entrada_tiene_proveedor_y_detalles()
    {
        $prov = Proveedor::factory()->create();
        $cat  = Categoria::factory()->create(['codigo' => 50]);
        $und  = Unidad::factory()->create();
        $prod = Producto::factory()->create([
            'codigo'    => $cat->codigo,
            'id_unidad' => $und->id_unidad,
        ]);

        $entrada = Entrada::factory()->create(['id_proveedor' => $prov->id_proveedor]);

        $detalle = DetalleEntrada::factory()->create([
            'id_entrada'  => $entrada->id_entrada,
            'id_producto' => $prod->id_producto,
            'cantidad'    => 10,
        ]);

        $this->assertEquals($prov->id_proveedor, $entrada->proveedor->id_proveedor);
        $this->assertCount(1, $entrada->detalles);
        $this->assertEquals($prod->id_producto, $entrada->detalles->first()->producto->id_producto);
    }
}
