<?php

namespace Tests\Feature\Entradas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Entrada;
use App\Models\DetalleEntrada;

class ValeEntradaPdfTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function genera_pdf_de_vale_entrada_para_id_valido()
    {
        // 1) Preparo rol y usuario
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo una entrada con al menos un detalle
        $entrada = Entrada::factory()
            ->has(DetalleEntrada::factory()->count(1), 'detalles')
            ->create();

        // 3) Llamada autenticada al endpoint PDF
        $response = $this->actingAs($user, 'sanctum')
                         ->get("/api/entradas/vales/{$entrada->id_entrada}?pdf=true");

        // 4) Aserciones
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        // Verifico que el contenido no esté vacío
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function vale_entrada_inexistente_para_pdf_devuelve_404()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Llamada a un ID que no existe con pdf=true
        $response = $this->actingAs($user, 'sanctum')
                         ->get('/api/entradas/vales/9999?pdf=true');

        $response->assertNotFound();
    }
}
