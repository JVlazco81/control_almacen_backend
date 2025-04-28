<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function autocompletarProveedor(Request $request)
    {
        $query = $request->input('query');
        $proveedores = Proveedor::where('nombre_proveedor', 'LIKE', "{$query}%")->orderBy('nombre_proveedor', 'asc')
            ->get('nombre_proveedor');

        return response()->json($proveedores);
    }
}
