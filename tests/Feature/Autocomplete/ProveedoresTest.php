<?php

namespace Tests\Feature\Autocomplete;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Proveedor;

class ProveedoresTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function devuelve_proveedores_que_coinciden_con_query()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        Proveedor::factory()->create(['nombre_proveedor' => 'Acme Corp']);
        Proveedor::factory()->create(['nombre_proveedor' => 'Beta Ltd']);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/proveedores?query=Ac');

        $response->assertOk()
                 ->assertJsonCount(1)
                 ->assertJsonFragment([
                     'nombre_proveedor' => 'Acme Corp',
                 ]);
    }

    /** @test */
    public function requiere_autenticacion()
    {
        $response = $this->getJson('/api/proveedores?query=Ac');
        $response->assertUnauthorized();
    }
}
