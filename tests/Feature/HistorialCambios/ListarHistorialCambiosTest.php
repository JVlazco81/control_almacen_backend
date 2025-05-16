<?php

namespace Tests\Feature\HistorialCambios;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\HistorialCambio;

class ListarHistorialCambiosTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lista_historial_de_cambios_con_usuario_relacionado()
    {
        // 1) Creo un producto para que id_auditado = 1 sea válido
        Producto::factory()->create(['id_producto' => 1]);

        // 2) Genero 3 registros de historial (factory usa id_auditado = 1 por defecto)
        HistorialCambio::factory()->count(3)->create();

        // 3) Usuario autenticado necesario para acceder
        $rol  = Rol::factory()->create(['rol' => 'almacenista']);
        $user = Usuario::factory()->create(['id_rol' => $rol->id_rol]);

        // 4) Llamada al endpoint
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/historial-cambios');

        // 5) Aserciones
        $response->assertOk()
                 ->assertJsonCount(3)
                 ->assertJsonStructure([
                     '*' => [
                         'id_historial',
                         'tipo_auditado',
                         'id_auditado',
                         'id_usuario',
                         'accion',
                         'valor_anterior',
                         'valor_nuevo',
                         'fecha',
                         'usuario' => [
                             'id_usuario',
                             'id_rol',
                             'primer_nombre',
                             'segundo_nombre',
                             'primer_apellido',
                             'segundo_apellido',
                         ],
                     ],
                 ]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_ver_historial_de_cambios()
    {
        $response = $this->getJson('/api/historial-cambios');

        $response->assertUnauthorized();
    }
}
