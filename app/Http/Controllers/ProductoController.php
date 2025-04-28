<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function autocompletarProducto(Request $request)
    {
        $query = $request->input('query');
        $productos = Producto::where('descripcion_producto', 'LIKE', "{$query}%")->orderBy('descripcion_producto', 'asc')
            ->get('descripcion_producto');

        return response()->json($productos);
    }
}
