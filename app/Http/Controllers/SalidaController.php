<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\Departamento;
use App\Models\Producto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\HistorialCambio;
use Illuminate\Support\Facades\Auth;

class SalidaController extends Controller
{
    public function generarVale(Request $request)
    {
        $data = $request->validate([
            'departamento' => 'required|string',
            'encargado' => 'required|string',
            'ordenCompra' => 'required|integer|min:1', // smallint range            'productos' => 'required|array|min:1',
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

                $stock = $p->cantidad;

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
                'orden_compra' => $data['ordenCompra']
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

            return response()->json([
                'message' => 'Vale de salida generado correctamente',
                'id_salida' => $salida->id_salida,
                'folio' => $salida->folio
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al generar vale de salida',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function index(){
        
        $salidas = Salida::with(['departamento', 'detalles.producto.unidad', 'detalles.producto.categoria'])
            ->orderBy('fecha_salida', 'desc')
            ->get();

        // Transformar la información para estructurar la respuesta
        // Transformar la información para estructurar la respuesta
        $salidasTransformadas = $salidas->map(function ($salida) {
            return [
                'id_salida' => $salida->id_salida,
                'departamento' => $salida->departamento->nombre_departamento,
                'folio' => $salida->folio,
                'salida_anual' => $salida->salida_anual,
                'fecha_salida' => $salida->fecha_salida,
                'orden_compra' => $salida->orden_compra,
                'productos' => $salida->detalles->map(function ($detalle) {
                    $producto = $detalle->producto;
                    return [
                        'id_producto' => $producto->id_producto,
                        'codigo' => $producto->codigo,
                        'descripcion' => $producto->descripcion_producto,
                        'marca' => $producto->marca,
                        'cantidad' => $detalle->cantidad,
                        'unidad' => $producto->unidad->tipo_unidad ?? 'N/A',
                        'categoria' => $producto->categoria->descripcion_categoria ?? 'N/A',
                        'precio' => $producto->precio,
                    ];
                }),
            ];
        });

        return response()->json($salidasTransformadas);
    }

    public function destroy($id){

        DB::beginTransaction();

        try{
            $salida = Salida::findOrFail($id);

            foreach($salida->detalles as $detalle){
                $detalle->delete();
            }
            
            $salida->delete();

            HistorialCambio::create([
                'tipo_auditado'  => 'Salida',
                'id_auditado'    => $salida->id_salida,
                'id_usuario'    => Auth::id(),
                'accion'         => 'eliminacion',
                'valor_anterior' => null,
                'valor_nuevo'    => null,
                'fecha'          => now(),
            ]);

            DB::commit();
            
            return response()->json(['message' => 'Salida eliminada correctamente'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Salida no encontrada'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la salida', 'details' => $e->getMessage()], 500);
        }
    }
}
