<?php
// tests/Unit/HistorialCambioTest.php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
