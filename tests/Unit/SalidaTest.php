<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Salida;
use App\Models\Departamento;
use App\Models\Usuario;
use App\Models\DetalleSalida;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;

class SalidaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function salida_tiene_departamento_y_detalles()
    {
        $dep  = Departamento::factory()->create();
        //$user = Usuario::factory()->create();
        $cat  = Categoria::factory()->create(['codigo' => 75]);
        $und  = Unidad::factory()->create();
        $prod = Producto::factory()->create([
            'codigo'    => $cat->codigo,
            'id_unidad' => $und->id_unidad,
        ]);

        $salida = Salida::factory()->create([
            'id_departamento' => $dep->id_departamento,
            //'id_usuario'      => $user->id_usuario,
        ]);

        $detalle = DetalleSalida::factory()->create([
            'id_salida'   => $salida->id_salida,
            'id_producto' => $prod->id_producto,
            'cantidad'    => 5,
        ]);

        $this->assertEquals($dep->id_departamento, $salida->departamento->id_departamento);
        //$this->assertEquals($user->id_usuario, $salida->usuario->id_usuario);
        $this->assertCount(1, $salida->detalles);
        $this->assertEquals($prod->id_producto, $salida->detalles->first()->producto->id_producto);
    }
}
