<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuario;
use App\Models\Rol;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_pertenece_a_rol()
    {
        $rol = Rol::factory()->create(['rol' => 'director']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        $this->assertEquals($rol->id_rol, $user->rol->id_rol);
    }
}
