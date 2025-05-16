<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logout_exitoso_revoca_el_token()
    {
        // 1) Creo rol y usuario, genero token
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create([
            'id_rol'           => $rol->id_rol,
            'usuario_password' => Hash::make('Secret@123'),
        ]);
        $token = $user->createToken('api-token')->plainTextToken;

        // 2) Llamada logout con Authorization header
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        // 3) Aserciones
        $response->assertOk()
                 ->assertJson(['message' => 'Sesión cerrada']);
    }

    /** @test */
    public function logout_sin_token_devuelve_401()
    {
        $response = $this->postJson('/api/logout');
        $response->assertUnauthorized();
    }
}
