<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;

class RolTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function rol_tiene_usuarios()
    {
        $rol = Rol::factory()->create(['rol' => 'almacenista']);

        $users = Usuario::factory()
            ->count(2)
            ->create(['id_rol' => $rol->id_rol]);

        $rol->refresh();

        $this->assertCount(2, $rol->usuarios);
        $this->assertTrue($rol->usuarios->contains($users[0]));
    }
}
