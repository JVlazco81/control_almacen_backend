<?php

namespace Tests\Feature\Entradas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Entrada;
use App\Models\DetalleEntrada;

class HistorialEntradasApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lista_entradas_con_sus_detalles_y_productos()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo 2 entradas, cada una con 3 detalles (y productos/units/categories automáticos)
        Entrada::factory()
            ->count(2)
            ->has(DetalleEntrada::factory()->count(3), 'detalles')
            ->create();

        // 3) Llamada al endpoint
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/entradas');

        // 4) Aserciones básicas
        $response->assertOk()
                 ->assertJsonCount(2) // 2 entradas
                 ->assertJsonStructure([
                     '*' => [
                         'id_entrada',
                         'folio',
                         'entrada_anual',
                         'proveedor',
                         'fecha_factura',
                         'fecha_entrada',
                         'nota',
                         'productos' => [
                             '*' => [
                                 'id_producto',
                                 'codigo',
                                 'descripcion',
                                 'marca',
                                 'cantidad',
                                 'unidad',
                                 'categoria',
                                 'precio',
                             ],
                         ],
                     ],
                 ]);

        // 5) Opcional: compruebo que uno de los id_entrada realmente existe en BD
        $primerId = $response->json()[0]['id_entrada'];
        $this->assertDatabaseHas('entradas', ['id_entrada' => $primerId]);
    }
}
