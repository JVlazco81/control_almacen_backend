<?php

namespace Tests\Feature\Autocomplete;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Departamento;

class DepartamentosTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function devuelve_departamentos_que_coinciden_con_query()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo dos departamentos
        Departamento::factory()->create(['nombre_departamento' => 'Alpha']);
        Departamento::factory()->create(['nombre_departamento' => 'Beta']);

        // 3) Petición con query="A"
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/departamentos?query=A');

        // 4) Aserciones corregidas
        $response->assertOk()
                 ->assertJsonCount(1) // Solo uno coincide
                 ->assertJsonFragment([
                     'nombre_departamento' => 'Alpha',
                 ]);
    }

    /** @test */
    public function requiere_autenticacion()
    {
        $response = $this->getJson('/api/departamentos?query=A');
        $response->assertUnauthorized();
    }
}