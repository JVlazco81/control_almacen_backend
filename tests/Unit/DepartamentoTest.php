<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Departamento;
use App\Models\Salida;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class DepartamentoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function departamento_tiene_salidas()
    {
        $dep = Departamento::factory()->create();

        $salidas = Salida::factory()
            ->count(2)
            ->create(['id_departamento' => $dep->id_departamento]);

        $dep->refresh();

        $this->assertCount(2, $dep->salidas);
        $this->assertTrue($dep->salidas->contains($salidas[0]));
    }

    /** @test */
    public function nombre_departamento_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Departamento::factory()->create([
            'nombre_departamento' => null,
        ]);
    }

    /** @test */
    public function nombre_departamento_no_puede_superar_55_caracteres()
    {
        $this->expectException(QueryException::class);
        Departamento::factory()->create([
            'nombre_departamento' => Str::repeat('B', 56),
        ]);
    }

    /** @test */
    public function nombre_encargado_no_puede_ser_null()
    {
        $this->expectException(QueryException::class);
        Departamento::factory()->create([
            'nombre_encargado' => null,
        ]);
    }

    /** @test */
    public function nombre_encargado_no_puede_superar_55_caracteres()
    {
        $this->expectException(QueryException::class);
        Departamento::factory()->create([
            'nombre_encargado' => Str::repeat('C', 56),
        ]);
    }
}
