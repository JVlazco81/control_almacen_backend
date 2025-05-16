<?php

namespace Tests\Feature\Entradas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Entrada;
use App\Models\DetalleEntrada;

class EliminarEntradaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function elimina_entrada_y_detalles_y_registra_historial()
    {
        // 1) Preparo rol y usuario
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Creo una entrada con 2 detalles
        $entrada = Entrada::factory()
            ->has(DetalleEntrada::factory()->count(2), 'detalles')
            ->create();

        // 3) Llamada DELETE autenticada
        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson("/api/entradas/{$entrada->id_entrada}");

        // 4) Aserciones respuesta
        $response->assertOk()
                 ->assertJson([
                     'message' => 'Entrada eliminada correctamente',
                 ]);

        // 5) Verifico soft delete en la tabla entradas
        $this->assertSoftDeleted('entradas', [
            'id_entrada' => $entrada->id_entrada,
        ]);

        // 6) Verifico soft delete en cada detalle
        foreach ($entrada->detalles as $detalle) {
            $this->assertSoftDeleted('detalleE', [
                'id_detalleE' => $detalle->id_detalleE,
            ]);
        }

        // 7) Verifico que se creó el registro en historial_cambios
        $this->assertDatabaseHas('historial_cambios', [
            'tipo_auditado' => 'Entrada',
            'id_auditado'   => $entrada->id_entrada,
            'accion'        => 'eliminacion',
        ]);
    }

    /** @test */
    public function eliminar_entrada_inexistente_devuelve_404()
    {
        // 1) Usuario autenticado
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 2) Llamada a un ID que no existe
        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson('/api/entradas/9999');

        $response->assertNotFound()
                 ->assertJson([
                     'error' => 'Entrada no encontrada',
                 ]);
    }
}
