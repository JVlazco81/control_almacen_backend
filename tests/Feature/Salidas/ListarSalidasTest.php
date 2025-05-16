<?php

namespace Tests\Feature\Salidas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Salida;
use App\Models\DetalleSalida;

class ListarSalidasTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lista_salidas_con_sus_detalles_y_productos()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo 2 salidas, cada una con 3 detalles
        Salida::factory()
            ->count(2)
            ->has(DetalleSalida::factory()->count(3), 'detalles')
            ->create();

        // 3) Llamada al endpoint
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/salidas');

        // 4) Aserciones
        $response->assertOk()
                 ->assertJsonCount(2)
                 ->assertJsonStructure([
                     '*' => [
                         'id_salida',
                         'departamento',
                         'folio',
                         'salida_anual',
                         'fecha_salida',
                         'orden_compra',
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
    }
}
