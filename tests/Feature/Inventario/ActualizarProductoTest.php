<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;

class ActualizarProductoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function actualiza_producto_parcialmente_y_registra_historial()
    {
        // 1) Usuario autenticado
        $rol     = Rol::factory()->create(['rol' => 'almacenista']);
        $user    = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo producto inicial (la factory crea categoría y unidad)
        $producto = Producto::factory()->create([
            'descripcion_producto' => 'Original',
            'marca'                => 'MarcaOld',
            'cantidad'             => 5,
            'precio'               => 10.00,
        ]);

        // 3) Preparo categoría y unidad nuevas para la actualización
        $categoriaNueva = Categoria::factory()->create();
        $unidadNueva    = Unidad::factory()->create();

        // 4) Payload de actualización parcial
        $payload = [
            'descripcion_producto' => 'Actualizado',
            'marca'                => 'MarcaNew',
            'cantidad'             => 10,
            'codigo'               => $categoriaNueva->codigo,
            'precio'               => 99.99,
            'id_unidad'            => $unidadNueva->id_unidad,
        ];

        // 5) Llamada PATCH /api/inventario/{id}
        $response = $this->actingAs($user, 'sanctum')
                         ->patchJson("/api/inventario/{$producto->id_producto}", $payload);

        // 6) Aserción de respuesta
        $response->assertOk()
                 ->assertJson([
                     'message' => 'Producto actualizado correctamente.'
                 ]);

        // 7) Verifico valores actualizados en BD
        $this->assertDatabaseHas('productos', [
            'id_producto'            => $producto->id_producto,
            'descripcion_producto'   => 'Actualizado',
            'marca'                  => 'MarcaNew',
            'cantidad'               => 10,
            'codigo'                 => $categoriaNueva->codigo,
            'precio'                 => 99.99,
            'id_unidad'              => $unidadNueva->id_unidad,
        ]);

        // 8) Verifico registro en historial_cambios
        $this->assertDatabaseHas('historial_cambios', [
            'tipo_auditado' => 'Producto',
            'id_auditado'   => $producto->id_producto,
            'accion'        => 'actualizacion',
        ]);
    }
}
