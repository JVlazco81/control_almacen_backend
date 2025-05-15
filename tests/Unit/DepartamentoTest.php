<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Departamento;
use App\Models\Salida;

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
}
