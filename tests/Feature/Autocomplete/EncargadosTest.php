<?php

namespace Tests\Feature\Autocomplete;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Departamento;

class EncargadosTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function devuelve_encargados_que_coinciden_con_query()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        Departamento::factory()->create(['nombre_encargado' => 'Carlos']);
        Departamento::factory()->create(['nombre_encargado' => 'Ana']);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/encargados?query=Ca');

        $response->assertOk()
                 ->assertJsonCount(1)
                 ->assertJsonFragment([
                     'nombre_encargado' => 'Carlos',
                 ]);
    }

    /** @test */
    public function requiere_autenticacion()
    {
        $response = $this->getJson('/api/encargados?query=Ca');
        $response->assertUnauthorized();
    }
}
