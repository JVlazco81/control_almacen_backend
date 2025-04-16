<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\Producto;
use App\Models\Unidad;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\HistorialCambio;
use Illuminate\Support\Facades\Auth;

class EntradaController extends Controller
{
    public function procesarEntrada(Request $request)
    {
        DB::beginTransaction();
        try {
            // Buscar o crear el proveedor
            $proveedor = Proveedor::firstOrCreate([
                'nombre_proveedor' => $request->proveedor
            ]);

            // Calcular el número de entrada anual
            $contador = Entrada::whereYear('fecha_entrada', date('Y'))->count() + 1;

            // Crear la entrada
            $entrada = Entrada::create([
                'id_proveedor' => $proveedor->id_proveedor,
                'folio' => $request->folio,
                'entrada_anual' => $contador,
                'fecha_factura' => date('Y-m-d', strtotime($request->fechaFactura)),
                'fecha_entrada' => now(),
                'nota' => $request->nota,
            ]);

            $productosRegistrados = [];

            // Agregar productos a la entrada y actualizar inventario
            foreach ($request->productos as $prod) {
                
                // Consultar la unidad; si no existe, se lanza error
                $unidad = Unidad::where('tipo_unidad', $prod['unidad'])->first();
                if (!$unidad) {
                    DB::rollBack();
                    throw new \Exception("La unidad '{$prod['unidad']}' no existe.");
                }

                // Buscar la categoría por claveProducto
                $categoria = Categoria::where('codigo', $prod['claveProducto'])->first();

                if (!$categoria) {
                    DB::rollBack();
                    throw new \Exception("La categoría con claveProducto {$prod['claveProducto']} no existe.");
                }

                // Buscar o crear el producto, usando el campo 'codigo' que relaciona con la categoría
                $producto = Producto::firstOrCreate([
                    'codigo' => $prod['claveProducto'],
                    'descripcion_producto' => $prod['descripcion'],
                    'marca' => $prod['marcaAutor']
                ], [
                    'id_unidad' => $unidad->id_unidad,
                    'cantidad' => 0,
                    'precio' => $prod['costo'],
                ]);

                // Agregar detalle de entrada
                DetalleEntrada::create([
                    'id_entrada' => $entrada->id_entrada,
                    'id_producto' => $producto->id_producto,
                    'cantidad' => $prod['cantidad'],
                ]);

                // Actualizar inventario sumando la cantidad ingresada
                $producto->cantidad += $prod['cantidad'];
                $producto->save();

                $productosRegistrados[] = [
                    'claveProducto' => $prod['claveProducto'],
                    'descripcion' => $producto->descripcion_producto,
                    'marcaAutor' => $producto->marca,
                    'unidad' => $unidad->tipo_unidad,
                    'cantidad' => $prod['cantidad'],
                    'costo' => $prod['costo'],
                    'total' => number_format($prod['cantidad'] * $prod['costo'], 2)
                ];
            }

            DB::commit();
            
            return response()->json([
                'id_entrada' => $entrada->id_entrada,
                'proveedor' => $proveedor->nombre_proveedor,
                'fechaFactura' => $entrada->fecha_factura,
                'folio' => $entrada->folio,
                'nota' => $entrada->nota,
                'productos' => $productosRegistrados
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar la entrada.', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            
            $entrada = Entrada::findOrFail($id);

            foreach ($entrada->detalles as $detalle) {
                $detalle->delete();
            }

            $entrada->delete();

            HistorialCambio::create([
                'tipo_auditado'  => 'Entrada',
                'id_auditado'    => $entrada->id_entrada,
                'id_usuario'    => Auth::id(),
                'accion'         => 'eliminacion',
                'valor_anterior' => null,
                'valor_nuevo'    => null,
                'fecha'          => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Entrada eliminada correctamente'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entrada no encontrada'], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al eliminar la entrada.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
