<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\Departamento;
use App\Models\Producto;

class SalidaController extends Controller
{
    public function generarVale(Request $request)
    {
        $data = $request->validate([
            'departamento' => 'required|string',
            'encargado' => 'required|string',
            'ordenCompra' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.folio' => 'required|string|max:15',
            'productos.*.descripcion' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Crear el departamento (ya que siempre se crea desde el formulario)
            $departamento = new Departamento();
            $departamento->nombre_departamento = $data['departamento'];
            $departamento->nombre_encargado = $data['encargado'];
            $departamento->save();

            // Obtener el siguiente número de salida anual
            $salidaAnual = Salida::max('salida_anual') + 1;

            // Validar stock antes de crear salida
            foreach ($data['productos'] as $producto) {
                $p = Producto::where('descripcion_producto', $producto['descripcion'])->first();

                if (!$p) {
                    return response()->json(['error' => 'Producto no encontrado: ' . $producto['descripcion']], 404);
                }

                $stock = $p->entradas()->sum('cantidad') - $p->detalleSalidas()->sum('cantidad');

                if ($stock < $producto['cantidad']) {
                    return response()->json([
                        'error' => "Stock insuficiente para {$producto['descripcion']}. Disponible: $stock, solicitado: {$producto['cantidad']}"
                    ], 400);
                }
            }

            // Crear una sola salida con todos los productos
            $salida = Salida::create([
                'id_departamento' => $departamento->id_departamento,
                'folio' => $data['productos'][0]['folio'],
                'salida_anual' => $salidaAnual,
                'fecha_salida' => now()->toDateString(),
                'orden_compra' => intval(date('md', strtotime($data['ordenCompra'])))
            ]);

            foreach ($data['productos'] as $producto) {
                $p = Producto::where('descripcion_producto', $producto['descripcion'])->first();

                // Crear detalle de salida
                DetalleSalida::create([
                    'id_salida' => $salida->id_salida,
                    'id_producto' => $p->id_producto,
                    'cantidad' => $producto['cantidad'],
                ]);

                // Descontar del inventario
                $p->cantidad -= $producto['cantidad'];
                $p->save();
            }

            DB::commit();

            return response()->json(['message' => 'Vale de salida generado correctamente', 'folio' => $salida->folio], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al generar vale de salida',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
