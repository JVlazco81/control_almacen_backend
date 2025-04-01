<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;

class HistorialEntradaController extends Controller
{
    public function obtenerHistorial()
    {
        // Obtener todas las entradas ordenadas por fecha de entrada (más reciente primero)
        $historial = Entrada::with('proveedor')
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
            ];
        });

        return response()->json($entradas);
    }
}
