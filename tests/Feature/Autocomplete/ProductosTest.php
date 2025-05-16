<?php

namespace Tests\Feature\Autocomplete;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Unidad;

class ProductosTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function devuelve_productos_que_coinciden_con_query()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        $cat  = Categoria::factory()->create(['codigo' => 200]);
        $unit = Unidad::factory()->create(['tipo_unidad' => 'pieza']);

        Producto::factory()->create([
            'codigo'               => $cat->codigo,
            'descripcion_producto' => 'Tornillo',
            'id_unidad'            => $unit->id_unidad,
        ]);
        Producto::factory()->create([
            'codigo'               => $cat->codigo,
            'descripcion_producto' => 'Clavo',
            'id_unidad'            => $unit->id_unidad,
        ]);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/productos?query=To');

        $response->assertOk()
                 ->assertJsonCount(1)
                 ->assertJsonFragment([
                     'descripcion_producto' => 'Tornillo',
                 ]);
    }

    /** @test */
    public function requiere_autenticacion()
    {
        $response = $this->getJson('/api/productos?query=To');
        $response->assertUnauthorized();
    }
}