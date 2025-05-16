<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Producto;

class ReporteInventarioPdfTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function genera_pdf_de_reporte_de_inventario()
    {
        // 1) Usuario autenticado
        $rol     = Rol::factory()->create(['rol' => 'almacenista']);
        $user    = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo algunos productos
        Producto::factory()->count(3)->create();

        // 3) Llamada GET /api/inventario/reporte
        $response = $this->actingAs($user, 'sanctum')
                         ->get('/api/inventario/reporte');

        // 4) Aserciones
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    }
}
