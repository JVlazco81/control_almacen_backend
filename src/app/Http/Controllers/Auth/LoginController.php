<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validar que se envíen 'primer_nombre', 'primer_apellido' y 'password'
        $request->validate([
            'primer_nombre'  => 'required',
            'primer_apellido'=> 'required',
            'password'       => 'required',
        ]);

        $primer_nombre = $request->input('primer_nombre');
        $primer_apellido = $request->input('primer_apellido');
        $password = $request->input('password');

        // Buscar el usuario utilizando el primer nombre y primer apellido
        $user = Usuario::where('primer_nombre', $primer_nombre)
                        ->where('primer_apellido', $primer_apellido)
                        ->first();

        if (!$user) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        // Validar la contraseña (se asume que está hasheada)
        if ($password !== $user->usuario_password) {
            return response()->json(['error' => 'Credenciales invalidas'], 401);
        }

        // Generar un token simulado (proximamente le agregare el token con Sanctum)
        $token = base64_encode(Str::random(40));

        return response()->json([
            'message' => 'Autenticacion exitosa',
            'token'   => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        // En un entorno real, aquí se invalidaría el token o se cerraría la sesión.
        return response()->json([
            'message' => 'Logout exitoso'
        ], 200);
    }
}

