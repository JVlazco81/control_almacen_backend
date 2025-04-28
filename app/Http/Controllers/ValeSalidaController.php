<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Salida;

class ValeSalidaController extends Controller
{
    public function generarVale(Request $request, $id)
    {
        $salida = Salida::with(['departamento', 'detalles.producto'])->find($id);

        if (!$salida) {
            return response()->json(["error" => "Salida no encontrada"], 404);
        }

        if ($request->query('pdf') === 'true') {
            try {
                $pdf = Pdf::loadView('vale_salida', compact('salida'));
                return $pdf->stream("vale_salida_{$salida->folio}.pdf");
            } catch (\Exception $e) {
                return response()->json(["error" => "Error al generar PDF", "details" => $e->getMessage()], 500);
            }
        }

        return response()->json([
            'folio' => $salida->folio,
            'fecha_salida' => $salida->fecha_salida,
            'orden_compra' => $salida->orden_compra,
            'departamento' => $salida->departamento->nombre_departamento,
            'encargado' => $salida->departamento->nombre_encargado,
            'productos' => $salida->detalles->map(function ($detalle) {
                return [
                    'codigo' => $detalle->producto->codigo,
                    'descripcion' => $detalle->producto->descripcion_producto,
                    'cantidad' => $detalle->cantidad
                ];
            })
        ]);
    }
}
