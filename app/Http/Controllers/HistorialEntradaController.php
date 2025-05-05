<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;

class HistorialEntradaController extends Controller
{
    public function obtenerHistorial()
{
        // Obtener todas las entradas con sus detalles y productos
        $historial = Entrada::with(['proveedor', 'detalles.producto.unidad', 'detalles.producto.categoria'])
            ->orderBy('fecha_entrada', 'desc')
            ->get();

        // Transformar la información para estructurar la respuesta
        $entradas = $historial->map(function ($entrada) {
            return [
                'id_entrada' => $entrada->id_entrada,
                'folio' => $entrada->folio,
                'entrada_anual' => $entrada->entrada_anual,
                'proveedor' => $entrada->proveedor->nombre_proveedor ?? 'Desconocido',
                'fecha_factura' => $entrada->fecha_factura,
                'fecha_entrada' => $entrada->fecha_entrada,
                'nota' => $entrada->nota,
                'productos' => $entrada->detalles->map(function ($detalle) {
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

        return response()->json($entradas);
    }
}
