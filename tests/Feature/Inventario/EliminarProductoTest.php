<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Producto;

class EliminarProductoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function elimina_producto_y_registra_historial()
    {
        // 1) Usuario autenticado
        $rol     = Rol::factory()->create(['rol' => 'almacenista']);
        $user    = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo un producto
        $producto = Producto::factory()->create();

        // 3) Llamada DELETE /api/inventario/{id}
        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson("/api/inventario/{$producto->id_producto}");

        // 4) Aserción de respuesta
        $response->assertOk()
                 ->assertJson([
                     'message' => 'Producto eliminado correctamente.'
                 ]);

        // 5) Soft delete en productos
        $this->assertSoftDeleted('productos', [
            'id_producto' => $producto->id_producto,
        ]);

        // 6) Registro en historial_cambios
        $this->assertDatabaseHas('historial_cambios', [
            'tipo_auditado' => 'Producto',
            'id_auditado'   => $producto->id_producto,
            'accion'        => 'eliminacion',
        ]);
    }
}
