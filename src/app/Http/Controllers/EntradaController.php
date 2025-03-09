<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\Producto;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    public function store(Request $request)
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
                // Buscar o crear la unidad
                $unidad = Unidad::firstOrCreate([
                    'tipo_unidad' => $prod['unidad']
                ]);

                // Buscar o crear el producto
                $producto = Producto::firstOrCreate([
                    'descripcion_producto' => $prod['descripcion'],
                    'marca' => $prod['marcaAutor']
                ], [
                    'codigo' => rand(1000, 9999), // Código aleatorio temporal
                    'id_unidad' => $unidad->id_unidad,
                    'cantidad' => 0, // Se actualizará después
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
}
