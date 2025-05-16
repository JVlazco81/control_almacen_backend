<?php

namespace Tests\Feature\Entradas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Entrada;
use App\Models\DetalleEntrada;

class ValeEntradaJsonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function muestra_vale_entrada_en_json_para_un_id_valido()
    {
        // 1) Preparo rol y usuario
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo una entrada con 2 detalles
        $entrada = Entrada::factory()
            ->has(DetalleEntrada::factory()->count(2), 'detalles')
            ->create([
                'folio'         => 'F-123',
                'entrada_anual' => 1,
                'fecha_factura' => '2025-05-15',
                'fecha_entrada' => '2025-05-16',
                'nota'          => 'Nota de prueba',
            ]);

        // 3) Llamada autenticada al endpoint JSON
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson("/api/entradas/vales/{$entrada->id_entrada}");

        // 4) Aserciones
        $response->assertOk()
                 ->assertJsonStructure([
                     'folio',
                     'entrada_anual',
                     'proveedor',
                     'fecha_factura',
                     'fecha_entrada',
                     'nota',
                     'productos' => [
                         '*' => [
                             'clave_producto',
                             'descripcion',
                             'cantidad',
                             'precio_unitario',
                             'total',
                         ],
                     ],
                 ])
                 ->assertJson([
                     'folio' => 'F-123',
                     'nota'  => 'Nota de prueba',
                 ]);
    }

    /** @test */
    public function vale_entrada_inexistente_devuelve_404()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Llamada a un ID que no existe
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/entradas/vales/999');

        $response->assertNotFound();
    }
}
