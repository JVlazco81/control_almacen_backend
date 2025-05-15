<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\DetalleEntrada;
use App\Models\Entrada;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;
use App\Models\Proveedor;

class DetalleEntradaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function detalle_entrada_relaciona_con_entrada_y_producto()
    {
        // Creamos todos los padres
        $prov = Proveedor::factory()->create();
        $cat  = Categoria::factory()->create(['codigo' => 88]);
        $und  = Unidad::factory()->create();
        $prod = Producto::factory()->create([
            'codigo'    => $cat->codigo,
            'id_unidad' => $und->id_unidad,
        ]);
        $ent = Entrada::factory()->create(['id_proveedor' => $prov->id_proveedor]);

        $detalle = DetalleEntrada::factory()->create([
            'id_entrada'  => $ent->id_entrada,
            'id_producto' => $prod->id_producto,
            'cantidad'    => 3,
        ]);

        $this->assertEquals($ent->id_entrada,  $detalle->entrada->id_entrada);
        $this->assertEquals($prod->id_producto, $detalle->producto->id_producto);
    }
}
