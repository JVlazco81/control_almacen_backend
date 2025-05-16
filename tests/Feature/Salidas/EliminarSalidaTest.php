<?php

namespace Tests\Feature\Salidas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\HistorialCambio;

class EliminarSalidaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function elimina_salida_y_detalles_y_registra_historial()
    {
        $rol    = Rol::factory()->create(['rol' => 'almacenista']);
        $user   = Usuario::factory()->create(['id_rol' => $rol->id_rol]);
        $salida = Salida::factory()
            ->has(DetalleSalida::factory()->count(2), 'detalles')
            ->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson("/api/salidas/{$salida->id_salida}");

        $response->assertOk()
                 ->assertJson(['message' => 'Salida eliminada correctamente']);

        // Soft delete en salidas
        $this->assertSoftDeleted('salidas', [
            'id_salida' => $salida->id_salida,
        ]);

        // Soft delete en cada detalle
        foreach ($salida->detalles as $detalle) {
            $this->assertSoftDeleted('detalle_salidas', [
                'id_detalle_salida' => $detalle->id_detalle_salida,
            ]);
        }

        // Registro en historial_cambios
        $this->assertDatabaseHas('historial_cambios', [
            'tipo_auditado' => 'Salida',
            'id_auditado'   => $salida->id_salida,
            'accion'        => 'eliminacion',
        ]);
    }

    /** @test */
    public function eliminar_salida_inexistente_devuelve_404()
    {
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson('/api/salidas/999');

        $response->assertNotFound()
                 ->assertJson(['error' => 'Salida no encontrada']);
    }
}
