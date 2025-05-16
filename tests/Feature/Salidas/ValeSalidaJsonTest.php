<?php

namespace Tests\Feature\Salidas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Salida;
use App\Models\DetalleSalida;

class ValeSalidaJsonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function muestra_vale_salida_en_json_para_id_valido()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo una salida con 2 detalles
        $salida = Salida::factory()
            ->has(DetalleSalida::factory()->count(2), 'detalles')
            ->create([
                'folio'        => 'S-500',
                'orden_compra' => 555,
                'fecha_salida' => '2025-05-15',
            ]);

        // 3) Llamada autenticada al endpoint JSON
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson("/api/salidas/vales/{$salida->id_salida}");

        // 4) Aserciones
        $response->assertOk()
                 ->assertJsonStructure([
                     'folio',
                     'fecha_salida',
                     'orden_compra',
                     'departamento',
                     'encargado',
                     'productos' => [
                         '*' => [
                             'codigo',
                             'descripcion',
                             'cantidad',
                         ],
                     ],
                 ])
                 ->assertJson([
                     'folio'        => 'S-500',
                     'orden_compra' => 555,
                 ]);
    }

    /** @test */
    public function vale_salida_inexistente_devuelve_404()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/salidas/vales/999');

        $response->assertNotFound();
    }
}