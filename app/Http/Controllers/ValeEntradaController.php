<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Entrada;

class ValeEntradaController extends Controller
{
    public function generarVale(Request $request, $id_entrada)
    {
        // Buscar la entrada con sus detalles y proveedor
        $entrada = Entrada::with(['proveedor', 'detalles.producto'])->find($id_entrada);

        // Si no se encuentra la entrada, devolver un error JSON
        if (!$entrada) {
            return response()->json(["error" => "Entrada no encontrada"], 404);
        }

        // Si la solicitud es para PDF
        if ($request->query('pdf') === 'true') {
            try {
                $pdf = Pdf::loadView('vale_entrada', compact('entrada'));
                return $pdf->stream("vale_entrada_{$entrada->folio}.pdf");
            } catch (\Exception $e) {
                return response()->json(["error" => "Error al generar PDF", "details" => $e->getMessage()], 500);
            }
        }

        // Si no es PDF, devolver la respuesta en JSON
        return response()->json([
            'folio' => $entrada->folio,
            'entrada_anual' => $entrada->entrada_anual,
            'proveedor' => $entrada->proveedor->nombre_proveedor ?? 'Desconocido',
            'fecha_factura' => $entrada->fecha_factura,
            'fecha_entrada' => $entrada->fecha_entrada,
            'nota' => $entrada->nota,
            'productos' => $entrada->detalles->map(function ($detalle) {
                return [
                    'clave_producto' => $detalle->producto->codigo,
                    'descripcion' => $detalle->producto->descripcion_producto,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => number_format($detalle->producto->precio, 2),
                    'total' => number_format($detalle->cantidad * $detalle->producto->precio, 2),
                ];
            })
        ]);
    }
}
