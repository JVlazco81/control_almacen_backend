<?php

namespace Tests\Feature\Entradas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\Unidad;

class ProcesarEntradaApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_usuario_autenticado_puede_crear_producto_mediante_procesar_entrada()
    {
        // 1) Creo rol y usuario
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Preparo categoría y unidad que el controlador espera
        Categoria::factory()->create([
            'codigo'               => 100,
            'descripcion_categoria'=> 'CatPrueba',
        ]);
        Unidad::factory()->create([
            'tipo_unidad' => 'caja',
        ]);

        // 3) Payload según EntradaController::procesarEntrada
        $payload = [
            'proveedor'    => 'Proveedor Test',
            'folio'        => 'F-100',
            'fechaFactura' => '2025-05-15',
            'nota'         => 'Entrada de prueba',
            'productos'    => [
                [
                    'claveProducto' => 100,
                    'descripcion'   => 'ProdPrueba',
                    'marcaAutor'    => 'MarcaTest',
                    'unidad'        => 'caja',
                    'cantidad'      => 5,
                    'costo'         => 12.34,
                ],
            ],
        ];

        // 4) Lanzo la petición autenticada
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/entradas', $payload);

        // 5) Aserciones sobre la respuesta
        $response->assertCreated()
                 ->assertJsonStructure([
                     'id_entrada',
                     'proveedor',
                     'fechaFactura',
                     'folio',
                     'nota',
                     'productos' => [
                         '*' => [
                             'claveProducto',
                             'descripcion',
                             'marcaAutor',
                             'unidad',
                             'cantidad',
                             'costo',
                             'total',
                         ],
                     ],
                 ]);

        // 6) Verifico en BD que:
        //   – Se creó el proveedor
        $this->assertDatabaseHas('proveedores', [
            'nombre_proveedor' => 'Proveedor Test',
        ]);

        //   – Se creó la entrada
        $this->assertDatabaseHas('entradas', [
            'folio' => 'F-100',
        ]);

        //   – Se creó el producto y su cantidad quedó en 5
        $this->assertDatabaseHas('productos', [
            'codigo'               => 100,
            'descripcion_producto' => 'ProdPrueba',
            'cantidad'             => 5,
            'precio'               => 12.34,
        ]);

        //   – Se creó al menos un detalle de entrada con cantidad 5
        $this->assertDatabaseHas('detalleE', [
            'cantidad' => 5,
        ]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_procesar_entrada()
    {
        $payload = [
            'proveedor'    => 'Proveedor X',
            'folio'        => 'F-200',
            'fechaFactura' => '2025-05-15',
            'nota'         => null,
            'productos'    => [
                [
                    'claveProducto' => 999,
                    'descripcion'   => 'NoImporta',
                    'marcaAutor'    => 'X',
                    'unidad'        => 'pieza',
                    'cantidad'      => 1,
                    'costo'         => 1.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/entradas', $payload);

        $response->assertUnauthorized(); // 401
    }
}
