<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use App\Models\DetalleSalida;
use App\Models\Salida;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;
use App\Models\Departamento;

class DetalleSalidaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function detalle_salida_relaciona_con_salida_y_producto()
    {
        $dep  = Departamento::factory()->create();
        //$user = Usuario::factory()->create();
        $cat  = Categoria::factory()->create(['codigo' => 99]);
        $und  = Unidad::factory()->create();
        $prod = Producto::factory()->create([
            'codigo'    => $cat->codigo,
            'id_unidad' => $und->id_unidad,
        ]);
        $sal = Salida::factory()->create([
            'id_departamento' => $dep->id_departamento,
            //'id_usuario'      => $user->id_usuario,
        ]);

        $detalle = DetalleSalida::factory()->create([
            'id_salida'   => $sal->id_salida,
            'id_producto' => $prod->id_producto,
            'cantidad'    => 8,
        ]);

        $this->assertEquals($sal->id_salida,   $detalle->salida->id_salida);
        $this->assertEquals($prod->id_producto, $detalle->producto->id_producto);
    }

    /** @test */
    public function id_salida_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        DetalleSalida::factory()->create([
            'id_salida' => null,
        ]);
    }

    /** @test */
    public function id_producto_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        DetalleSalida::factory()->create([
            'id_producto' => null,
        ]);
    }

    /** @test */
    public function cantidad_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        DetalleSalida::factory()->create([
            'cantidad' => null,
        ]);
    }
}
