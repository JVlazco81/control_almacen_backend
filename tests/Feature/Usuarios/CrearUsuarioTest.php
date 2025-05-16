<?php

namespace Tests\Feature\Usuarios;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;

class CrearUsuarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function director_puede_crear_usuario_con_datos_validos()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $creator     = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);
        $otroRol     = Rol::factory()->create(['rol' => 'almacenista']);

        $payload = [
            'id_rol'           => $otroRol->id_rol,
            'primer_nombre'    => 'Juan',
            'segundo_nombre'   => 'Carlos',
            'primer_apellido'  => 'Pérez',
            'segundo_apellido' => 'Gómez',
            'usuario_password' => 'Abc@1234',
        ];

        $response = $this->actingAs($creator, 'sanctum')
                         ->postJson('/api/usuarios', $payload);

        $response->assertCreated()
                 ->assertJson([
                     'message' => 'Usuario registrado correctamente',
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'primer_nombre' => 'Juan',
            'primer_apellido'=> 'Pérez',
        ]);
    }

    /** @test */
    public function falla_validacion_si_contraseña_no_cumple_regex()
    {
        $rolDirector = Rol::factory()->create(['rol' => 'director']);
        $creator     = Usuario::factory()->create(['id_rol' => $rolDirector->id_rol]);

        $payload = [
            'id_rol'           => $rolDirector->id_rol,
            'primer_nombre'    => 'Ana',
            'primer_apellido'  => 'López',
            'segundo_apellido' => 'Ramírez',
            'usuario_password' => 'sinMayuscula1', // falla regex
        ];

        $response = $this->actingAs($creator, 'sanctum')
                         ->postJson('/api/usuarios', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['usuario_password']);
    }

    /** @test */
    public function almacenista_no_puede_crear_usuario()
    {
        $rolAlmacen = Rol::factory()->create(['rol' => 'almacenista']);
        $user       = Usuario::factory()->create(['id_rol' => $rolAlmacen->id_rol]);
        $otroRol    = Rol::factory()->create(['rol' => 'almacenista']);

        $payload = [
            'id_rol'           => $otroRol->id_rol,
            'primer_nombre'    => 'X',
            'primer_apellido'  => 'Y',
            'segundo_apellido' => 'Z',
            'usuario_password' => 'Abc@1234',
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/usuarios', $payload);

        $response->assertForbidden();
    }

    /** @test */
    public function no_autenticado_recibe_401()
    {
        $response = $this->postJson('/api/usuarios', []);
        $response->assertUnauthorized();
    }
}
