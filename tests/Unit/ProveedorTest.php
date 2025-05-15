<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Proveedor;
use App\Models\Entrada;

class ProveedorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function proveedor_tiene_entradas()
    {
        $prov = Proveedor::factory()->create();

        $entradas = Entrada::factory()
            ->count(3)
            ->create(['id_proveedor' => $prov->id_proveedor]);

        $prov->refresh();

        $this->assertCount(3, $prov->entradas);
        $this->assertTrue($prov->entradas->contains($entradas[0]));
    }
}
