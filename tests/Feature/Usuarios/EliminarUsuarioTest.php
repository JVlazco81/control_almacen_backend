<?php

namespace Tests\Feature\Usuarios;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;

class EliminarUsuarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function director_puede_eliminar_usuario_y_soft_delete()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        Usuario::factory()->count(2)->create(['id_rol' => $rolDirector->id_rol]);
        $admin       = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);
        $toDelete    = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson("/api/usuarios/{$toDelete->id_usuario}");

        $response->assertOk()
                 ->assertJson(['message' => 'Usuario eliminado correctamente']);

        $this->assertSoftDeleted('usuarios', [
            'id_usuario' => $toDelete->id_usuario,
        ]);
    }

    /** @test */
    public function no_permite_eliminar_al_unico_director()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $soloDirector = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $response = $this->actingAs($soloDirector, 'sanctum')
                         ->deleteJson("/api/usuarios/{$soloDirector->id_usuario}");

        $response->assertForbidden()
                 ->assertJson(['error' => 'No puedes eliminar este usuario porque es el único director.']);
    }

    /** @test */
    public function director_recibe_404_al_eliminar_usuario_inexistente()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $admin       = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson('/api/usuarios/9999');

        $response->assertNotFound()
                 ->assertJson(['error' => 'Usuario no encontrado']);
    }

    /** @test */
    public function almacenista_no_puede_eliminar_usuario()
    {
        $rolAlmacen = Rol::factory()->create(['rol' => 'almacenista']);
        $user       = Usuario::factory()->create(['id_rol' => $rolAlmacen->id_rol]);

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson('/api/usuarios/1');

        $response->assertForbidden();
    }

    /** @test */
    public function no_autenticado_recibe_401_al_eliminar()
    {
        $response = $this->deleteJson('/api/usuarios/1');
        $response->assertUnauthorized();
    }
}
