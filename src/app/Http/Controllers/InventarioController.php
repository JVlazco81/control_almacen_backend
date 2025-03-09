<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function obtenerInventario()
    {
        // Obtener todos los productos con sus relaciones
        $productos = Producto::with(['unidad', 'categoria'])->get();

        // Mapear los datos para calcular los valores necesarios
        $inventario = $productos->map(function ($producto, $index) {
            $subtotal = $producto->cantidad * $producto->precio;
            $iva = $subtotal * 0.16;
            $total = $subtotal + $iva;

            return [
                'num' => $index + 1,
                'clave_producto' => $producto->codigo,
                'descripcion' => $producto->descripcion_producto,
                'marca_autor' => $producto->marca,
                'categoria' => $producto->categoria ? $producto->categoria->descripcion_categoria : 'Sin categoría',
                'unidad' => $producto->unidad ? $producto->unidad->tipo_unidad : 'No definida',
                'existencias' => $producto->cantidad,
                'costo_por_unidad' => number_format($producto->precio, 2),
                'subtotal' => number_format($subtotal, 2),
                'iva' => number_format($iva, 2),
                'monto_total' => number_format($total, 2)
            ];
        });

        return response()->json($inventario);
    }
}
