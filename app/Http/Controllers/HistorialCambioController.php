<?php

namespace App\Http\Controllers;

use App\Models\HistorialCambio;
use Illuminate\Http\Request;

class HistorialCambioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $historialCambios = HistorialCambio::with('usuario')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($historialCambios);
    }
}
