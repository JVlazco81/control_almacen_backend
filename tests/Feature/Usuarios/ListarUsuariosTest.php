<?php

namespace Tests\Feature\Usuarios;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;

class ListarUsuariosTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function director_puede_listar_usuarios()
    {
        // Creo rol director y dos usuarios con ese rol
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $usuarios    = Usuario::factory()->count(2)->create(['id_rol' => $rolDirector->id_rol]);

        // Autentico como uno de ellos
        $this->actingAs($usuarios->first(), 'sanctum');

        $response = $this->getJson('/api/usuarios');

        $response->assertOk()
                 ->assertJsonCount(2)
                 ->assertJsonStructure([
                     '*' => [
                         'id_usuario',
                         'id_rol',
                         'primer_nombre',
                         'segundo_nombre',
                         'primer_apellido',
                         'segundo_apellido',
                         'deleted_at',
                     ],
                 ]);
    }

    /** @test */
    public function almacenista_no_puede_listar_usuarios()
    {
        $rolAlmacen = Rol::factory()->create(['rol' => 'almacenista']);
        $user       = Usuario::factory()->create(['id_rol' => $rolAlmacen->id_rol]);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/usuarios');

        $response->assertForbidden();
    }

    /** @test */
    public function no_autenticado_recibe_401()
    {
        $response = $this->getJson('/api/usuarios');
        $response->assertUnauthorized();
    }
}