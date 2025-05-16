<?php

namespace Tests\Feature\Salidas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Producto;

class CrearSalidaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function crea_vale_de_salida_exitosamente()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo un producto con stock inicial 10
        $producto = Producto::factory()->create([
            'descripcion_producto' => 'ProdSalida',
            'cantidad'             => 10,
        ]);

        // 3) Payload para POST /api/salidas
        $payload = [
            'departamento' => 'DeptTest',
            'encargado'    => 'EncTest',
            'ordenCompra'  => 1234,
            'productos'    => [
                [
                    'folio'       => 'S-0001',
                    'descripcion' => 'ProdSalida',
                    'cantidad'    => 5,
                ],
            ],
        ];

        // 4) Petición autenticada
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/salidas', $payload);

        // 5) Aserciones sobre la respuesta
        $response->assertCreated()
                 ->assertJson([
                     'message' => 'Vale de salida generado correctamente',
                     'folio'   => 'S-0001',
                 ]);

        // 6) Verifico en BD:
        //   – Departamento creado
        $this->assertDatabaseHas('departamentos', [
            'nombre_departamento' => 'DeptTest',
            'nombre_encargado'    => 'EncTest',
        ]);

        //   – Salida creada
        $this->assertDatabaseHas('salidas', [
            'folio'        => 'S-0001',
            'orden_compra' => 1234,
        ]);

        //   – Detalle de salida
        $this->assertDatabaseHas('detalle_salidas', [
            'cantidad' => 5,
        ]);

        //   – Stock del producto actualizado (10 - 5 = 5)
        $this->assertDatabaseHas('productos', [
            'id_producto'          => $producto->id_producto,
            'cantidad'             => 5,
        ]);
    }

    /** @test */
    public function producto_no_encontrado_devuelve_404()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        $payload = [
            'departamento' => 'D1',
            'encargado'    => 'E1',
            'ordenCompra'  => 1,
            'productos'    => [
                [
                    'folio'       => 'S1',
                    'descripcion' => 'NoExiste',
                    'cantidad'    => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/salidas', $payload);

        $response->assertNotFound()
                 ->assertJson([
                     'error' => 'Producto no encontrado: NoExiste',
                 ]);
    }

    /** @test */
    public function stock_insuficiente_devuelve_400()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // Producto con stock 2
        $producto = Producto::factory()->create([
            'descripcion_producto' => 'P',
            'cantidad'             => 2,
        ]);

        $payload = [
            'departamento' => 'D',
            'encargado'    => 'E',
            'ordenCompra'  => 1,
            'productos'    => [
                [
                    'folio'       => 'F',
                    'descripcion' => 'P',
                    'cantidad'    => 5,
                ],
            ],
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/salidas', $payload);

        $response->assertStatus(400)
                 ->assertJson([
                     'error' => "Stock insuficiente para P. Disponible: {$producto->cantidad}, solicitado: 5"
                 ]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_crear_salida()
    {
        $payload = [
            'departamento' => 'D',
            'encargado'    => 'E',
            'ordenCompra'  => 1,
            'productos'    => [
                [
                    'folio'       => 'F',
                    'descripcion' => 'X',
                    'cantidad'    => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/salidas', $payload);

        $response->assertUnauthorized();
    }
}
