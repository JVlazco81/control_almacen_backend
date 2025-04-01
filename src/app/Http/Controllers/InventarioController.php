<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\HistorialCambio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


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
    /**
     * Actualiza los datos de un producto en el inventario.
     *
     * Se espera recibir en el request los siguientes campos:
     * - descripcion_producto (Nombre/Descripción)
     * - marca (Marca/Autor)
     * - cantidad (Cantidad)
     * - codigo (Clasificación)
     * - precio (Precio unitario)
     * - id_unidad (Unidad de medida)
     *
     * La vista general se actualiza automáticamente en base a la respuesta del API.
     * Además, se registra el cambio en el historial de cambios.
     */
    public function actualizarProducto(Request $request, $id)
{
    // Validar solo los campos que se envíen en el request (actualización parcial)
    $request->validate([
        'descripcion_producto' => 'sometimes|string|max:100',
        'marca'                => 'sometimes|string|max:100',
        'cantidad'             => 'sometimes|integer|min:0',
        'codigo'               => 'sometimes|integer',
        'precio'               => 'sometimes|numeric|min:0',
        'id_unidad'            => 'sometimes|integer',
    ]);

    DB::beginTransaction();
    try {
        // Obtener el producto actual
        $producto = Producto::findOrFail($id);

        // Capturar el estado previo del producto (solo los campos que se podrían modificar)
        $estadoAnterior = $producto->only([
            'descripcion_producto',
            'marca',
            'cantidad',
            'codigo',
            'precio',
            'id_unidad'
        ]);

        // Actualizar solo los campos que fueron enviados
        $producto->update($request->only([
            'descripcion_producto',
            'marca',
            'cantidad',
            'codigo',
            'precio',
            'id_unidad'
        ]));

        // Capturar el estado nuevo del producto luego de la actualización
        $estadoNuevo = $producto->only([
            'descripcion_producto',
            'marca',
            'cantidad',
            'codigo',
            'precio',
            'id_unidad'
        ]);

        // Registrar la acción en el historial de cambios
        HistorialCambio::create([
            'tipo_auditado' => 'Producto',           // En lugar de 'auditable_type'
            'id_auditado'   => $producto->id_producto, // En lugar de 'auditable_id'
            'id_usuario'   => Auth::id(),            // Asegúrate de usar el nombre correcto (id_usuarios)
            'accion'        => 'actualizacion',
            'valor_anterior'=> json_encode($estadoAnterior),
            'valor_nuevo'   => json_encode($estadoNuevo),
            'fecha'         => now(),
        ]);        

        DB::commit();
        return response()->json(['message' => 'Producto actualizado correctamente.'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al actualizar el producto: ' . $e->getMessage()], 500);
    }
}

    /**
     * Elimina de forma lógica un producto del inventario.
     *
     * La eliminación es lógica (soft delete), por lo que el registro no se elimina físicamente.
     * Además, se actualiza la vista general del inventario (disminuyendo la cantidad del artículo)
     * y se registra el cambio en el historial de cambios.
     */
    public function eliminarProducto(Request $request, $id)
    {
        // Aquí se asume que la confirmación se realiza en el frontend.
        DB::beginTransaction();
        try {
            $producto = Producto::findOrFail($id);

            // Capturar el estado actual del producto antes de eliminarlo
            $estadoAnterior = [
                'descripcion_producto' => $producto->descripcion_producto,
                'marca' => $producto->marca,
                'cantidad' => $producto->cantidad,
                'codigo' => $producto->codigo,
                'precio' => $producto->precio,
                'id_unidad' => $producto->id_unidad,
            ];

            // Realizar eliminación lógica (soft delete)
            $producto->delete();

            // Registrar la acción de eliminación en el historial de cambios.
            // En este caso, valor_nuevo se deja como null para indicar eliminación.
            HistorialCambio::create([
                'tipo_auditado' => 'Producto',
                'id_auditado'   => $producto->id_producto,
                'id_usuario'   => Auth::id(),
                'accion'        => 'eliminacion',
                'valor_anterior'=> json_encode($estadoAnterior),
                'valor_nuevo'   => null,
                'fecha'         => now(),
            ]);            

            DB::commit();
            return response()->json(['message' => 'Producto eliminado correctamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar el producto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Genera un reporte PDF del inventario.
     *
     * El reporte incluye:
     * - Lista completa de productos con Clave, Descripción, Marca, Unidad, Existencias,
     *   Costo por unidad, Sub-total, IVA y Monto Total.
     * - Fecha y hora de generación.
     * - Nombre del usuario que generó el reporte.
     * - Espacio para firma.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generarReporteInventario(Request $request)
    {
        // Obtener el inventario completo con la relación de unidad
        $inventario = Producto::with('unidad')->get();

        // Obtener la fecha y hora actual, formateada según se requiera
        $fecha = Carbon::now()->format('d/m/Y H:i:s');

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Renderizar la vista del reporte a HTML (se debe crear la vista "reporte_inventario.blade.php")
        $html = view('reporte_inventario', compact('inventario', 'fecha', 'user'))->render();

        try {
            // Generar el PDF a partir del HTML
            $pdf = Pdf::loadHTML($html);

            // Retornar el PDF en streaming
            return $pdf->stream("reporte_inventario_" . Carbon::now()->format('Ymd_His') . ".pdf");
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar PDF',
                'details' => $e->getMessage()
            ], 500);
        }
    }


}
