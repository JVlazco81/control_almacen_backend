<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function autocompletarDepartamento(Request $request)
    {
        $query = $request->input('query');
        $departamentos = Departamento::where('nombre_departamento', 'LIKE', "{$query}%")->orderBy('nombre_departamento', 'asc')
            ->get('nombre_departamento');

        return response()->json($departamentos);
    }

    public function autocompletarEncargado(Request $request)
    {
        $query = $request->input('query');
        $departamentos = Departamento::where('nombre_encargado', 'LIKE', "{$query}%")->orderBy('nombre_encargado', 'asc')
            ->get('nombre_encargado');

        return response()->json($departamentos);
    }
}
