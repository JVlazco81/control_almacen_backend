<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
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

    public function id_rol_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create(['id_rol' => null]);
    }

    /** @test */
    public function primer_nombre_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create(['primer_nombre' => null]);
    }

    /** @test */
    public function primer_nombre_no_puede_superar_20_caracteres()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create([
            'primer_nombre' => Str::repeat('A', 21),
        ]);
    }

    /** @test */
    public function segundo_nombre_no_puede_superar_40_caracteres()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create([
            'segundo_nombre' => Str::repeat('B', 41),
        ]);
    }

    /** @test */
    public function primer_apellido_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create(['primer_apellido' => null]);
    }

    /** @test */
    public function primer_apellido_no_puede_superar_25_caracteres()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create([
            'primer_apellido' => Str::repeat('C', 26),
        ]);
    }

    /** @test */
    public function segundo_apellido_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create(['segundo_apellido' => null]);
    }

    /** @test */
    public function segundo_apellido_no_puede_superar_25_caracteres()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create([
            'segundo_apellido' => Str::repeat('D', 26),
        ]);
    }

    /** @test */
    public function usuario_password_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create(['usuario_password' => null]);
    }

    /** @test */
    public function usuario_password_no_puede_superar_255_caracteres()
    {
        $this->expectException(QueryException::class);
        Usuario::factory()->create([
            'usuario_password' => Str::repeat('E', 256),
        ]);
    }
}
