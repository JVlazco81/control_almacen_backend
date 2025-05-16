<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_exitoso_con_credenciales_validas()
    {
        // 1) Creo rol y usuario con contraseña conocida
        $rol = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create([
            'id_rol'           => $rol->id_rol,
            'primer_nombre'    => 'Juan',
            'primer_apellido'  => 'Pérez',
            'usuario_password' => Hash::make('Secret@123'),
        ]);

        // 2) Payload de login
        $payload = [
            'primer_nombre'   => 'Juan',
            'primer_apellido' => 'Pérez',
            'usuario_password'=> 'Secret@123',
        ];

        // 3) Petición
        $response = $this->postJson('/api/login', $payload);

        // 4) Aserciones
        $response->assertOk()
                 ->assertJsonStructure([
                     'message',
                     'token',
                     'user' => [
                         'id_usuario',
                         'primer_nombre',
                         'primer_apellido',
                         // …otros campos del usuario…
                     ],
                 ])
                 ->assertJson(['message' => 'Login exitoso']);
    }

    /** @test */
    public function login_fallido_con_credenciales_invalidas()
    {
        // 1) Creo usuario con otra contraseña
        $rol = Rol::factory()->create(['rol' => 'almacenista']);
        Usuario::factory()->create([
            'id_rol'           => $rol->id_rol,
            'primer_nombre'    => 'Juan',
            'primer_apellido'  => 'Pérez',
            'usuario_password' => Hash::make('Secret@123'),
        ]);

        // 2) Intento de login con pass incorrecta
        $payload = [
            'primer_nombre'   => 'Juan',
            'primer_apellido' => 'Pérez',
            'usuario_password'=> 'WrongPass',
        ];

        $response = $this->postJson('/api/login', $payload);

        // 3) Debe ser 401 con mensaje de error
        $response->assertUnauthorized()
                 ->assertJson(['error' => 'Credenciales inválidas']);
    }
}
