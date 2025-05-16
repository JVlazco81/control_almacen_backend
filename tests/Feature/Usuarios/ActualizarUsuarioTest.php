<?php

namespace Tests\Feature\Usuarios;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;

class ActualizarUsuarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function director_puede_actualizar_campos_de_usuario()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $usuario     = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);
        $target      = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $payload = [
            'primer_nombre' => 'NuevoNombre',
            'usuario_password' => 'Zxy@9876',
        ];

        $response = $this->actingAs($usuario, 'sanctum')
                         ->patchJson("/api/usuarios/{$target->id_usuario}", $payload);

        $response->assertOk()
                 ->assertJson(['message' => 'Usuario actualizado correctamente']);

        $this->assertDatabaseHas('usuarios', [
            'id_usuario'    => $target->id_usuario,
            'primer_nombre' => 'NuevoNombre',
        ]);
    }

    /** @test */
    public function valida_regex_contraseña_al_actualizar()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $usuario     = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);
        $target      = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $payload = ['usuario_password' => 'sinEspecial']; // no cumple

        $response = $this->actingAs($usuario, 'sanctum')
                         ->patchJson("/api/usuarios/{$target->id_usuario}", $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['usuario_password']);
    }

    /** @test */
    public function no_permite_cambiar_ultimo_director()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        // Solo un director en BD
        $soloDirector = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $payload = ['id_rol' => $rolDirector->id_rol + 1]; // intento cambiar a otro rol

        $response = $this->actingAs($soloDirector, 'sanctum')
                         ->patchJson("/api/usuarios/{$soloDirector->id_usuario}", $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['id_rol']);
    }

    /** @test */
    public function almacenista_no_puede_actualizar_usuario()
    {
        $rolAlmacen = Rol::factory()->create(['rol' => 'almacenista']);
        $user       = Usuario::factory()->create(['id_rol' => $rolAlmacen->id_rol]);
        $target     = Usuario::factory()->create(['id_rol' => $rolAlmacen->id_rol]);

        $response = $this->actingAs($user, 'sanctum')
                         ->patchJson("/api/usuarios/{$target->id_usuario}", ['primer_nombre' => 'X']);

        $response->assertForbidden();
    }

    /** @test */
    public function no_autenticado_recibe_401_al_actualizar()
    {
        $response = $this->patchJson('/api/usuarios/1', ['primer_nombre' => 'X']);
        $response->assertUnauthorized();
    }
}
