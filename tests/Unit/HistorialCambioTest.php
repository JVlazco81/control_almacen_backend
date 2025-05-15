<?php
// tests/Unit/HistorialCambioTest.php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Models\HistorialCambio;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;  

class HistorialCambioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function historial_cambio_relaciona_con_usuario_y_auditable()
    {
        $user = Usuario::factory()->create();

        // 1) Creamos un producto que será auditado
        $product = Producto::factory()->create([
            'codigo'    => Categoria::factory()->create()->codigo,
            'id_unidad' => Unidad::factory()->create()->id_unidad,
        ]);

        // 2) Ahora creamos el HistorialCambio usando el FQCN
        $hist = HistorialCambio::factory()->create([
            'id_usuario'    => $user->id_usuario,
            'tipo_auditado' => Producto::class,
            'id_auditado'   => $product->id_producto,
        ]);

        $this->assertEquals($user->id_usuario, $hist->usuario->id_usuario);

        // Ahora auditable() resolverá correctamente a App\Models\Producto
        $this->assertInstanceOf(Producto::class, $hist->auditable);
    }

    /** @test */
    public function tipo_auditado_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        HistorialCambio::factory()->create([
            'tipo_auditado' => null,
        ]);
    }

    /** @test */
    public function tipo_auditado_no_puede_superar_30_caracteres()
    {
        $this->expectException(QueryException::class);

        HistorialCambio::factory()->create([
            'tipo_auditado' => Str::repeat('X', 31),
        ]);
    }

    /** @test */
    public function id_auditado_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        HistorialCambio::factory()->create([
            'id_auditado' => null,
        ]);
    }

    /** @test */
    public function id_usuario_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        HistorialCambio::factory()->create([
            'id_usuario' => null,
        ]);
    }

    /** @test */
    public function accion_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        HistorialCambio::factory()->create([
            'accion' => null,
        ]);
    }

    /** @test */
    public function accion_no_puede_superar_20_caracteres()
    {
        $this->expectException(QueryException::class);

        HistorialCambio::factory()->create([
            'accion' => Str::repeat('A', 21),
        ]);
    }

    /** @test */
    public function fecha_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);

        HistorialCambio::factory()->create([
            'fecha' => null,
        ]);
    }
}
