<?php

namespace Tests\Feature\Salidas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Salida;
use App\Models\DetalleSalida;

class ValeSalidaPdfTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function genera_pdf_de_vale_salida_para_id_valido()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        $salida = Salida::factory()
            ->has(DetalleSalida::factory()->count(1), 'detalles')
            ->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->get("/api/salidas/vales/{$salida->id_salida}?pdf=true");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function vale_salida_pdf_inexistente_devuelve_404()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        $response = $this->actingAs($user, 'sanctum')
                         ->get('/api/salidas/vales/9999?pdf=true');

        $response->assertNotFound();
    }
}