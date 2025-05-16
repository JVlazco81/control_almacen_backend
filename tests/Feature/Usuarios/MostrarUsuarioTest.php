<?php

namespace Tests\Feature\Usuarios;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;

class MostrarUsuarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function director_puede_ver_un_usuario_existente()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $usuarios    = Usuario::factory()->count(2)->create(['id_rol' => $rolDirector->id_rol]);
        $viewer      = $usuarios->first();
        $toShow      = $usuarios->last();

        $response = $this->actingAs($viewer, 'sanctum')
                         ->getJson("/api/usuarios/{$toShow->id_usuario}");

        $response->assertOk()
                 ->assertJson([
                     'id_usuario'     => $toShow->id_usuario,
                     'primer_nombre'  => $toShow->primer_nombre,
                     'primer_apellido'=> $toShow->primer_apellido,
                 ]);
    }

    /** @test */
    public function director_recibe_404_si_usuario_no_existe()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $admin       = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/usuarios/9999');

        $response->assertNotFound()
                 ->assertJson(['error' => 'Usuario no encontrado']);
    }

    /** @test */
    public function almacenista_no_puede_ver_usuario()
    {
        $rolAlmacen = Rol::factory()->create(['rol' => 'almacenista']);
        $user       = Usuario::factory()->create(['id_rol' => $rolAlmacen->id_rol]);
        $anyId      = 1;

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson("/api/usuarios/{$anyId}");

        $response->assertForbidden();
    }

    /** @test */
    public function no_autenticado_recibe_401()
    {
        $response = $this->getJson('/api/usuarios/1');
        $response->assertUnauthorized();
    }
}
