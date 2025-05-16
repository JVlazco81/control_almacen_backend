<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Producto;

class ObtenerInventarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function obtiene_inventario_con_campos_calculados()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo 2 productos con cantidad y precio conocidos
        Producto::factory()->count(2)->create([
            'cantidad' => 5,
            'precio'   => 10.00,
        ]);

        // 3) Llamada GET /api/inventario
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/inventario');

        // 4) Aserciones de estructura y conteo
        $response->assertOk()
                 ->assertJsonCount(2)
                 ->assertJsonStructure([
                     '*' => [
                         'num',
                         'clave_producto',
                         'descripcion',
                         'marca_autor',
                         'categoria',
                         'unidad',
                         'existencias',
                         'costo_por_unidad',
                         'subtotal',
                         'iva',
                         'monto_total',
                     ],
                 ]);

        // 5) Verifico cálculos en el primer elemento
        $item = $response->json()[0];
        $this->assertEquals('50.00', $item['subtotal']);    // 5 * 10
        $this->assertEquals('8.00',  $item['iva']);         // 50 * 0.16
        $this->assertEquals('58.00', $item['monto_total']);  // 50 + 8
    }
}
