<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
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

    /** @test */
    public function rol_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Rol::factory()->create(['rol' => null]);
    }

    /** @test */
    public function rol_debe_ser_valor_valido_del_enum()
    {
        $this->expectException(QueryException::class);
        // 'superusuario' no está en ['almacenista','director']
        Rol::factory()->create(['rol' => 'superusuario']);
    }
}
