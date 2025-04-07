<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // public function login(Request $request)
    // {
    //     $user = Usuario::where('primer_nombre', $request->primer_nombre)
    //            ->where('primer_apellido', $request->primer_apellido)
    //            ->first();

    //     if (!$user || $user->usuario_password !== $request->usuario_password) {
    //         return response()->json(['message' => 'Credenciales incorrectas'], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;
        
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //         'user' => $user
    //     ])->header('Content-Type', 'application/json; charset=UTF-8');

    // }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'primer_nombre'   => 'required|string',
            'primer_apellido' => 'required|string',
            'usuario_password'        => 'required|string',
        ]);

        //Obtener todos los usuarios con ese nombre y apellido
        $candidatos = Usuario::where('primer_nombre', $credentials['primer_nombre'])
                             ->where('primer_apellido', $credentials['primer_apellido'])
                             ->get();

        //Buscar el primer usuario cuya contraseña coincida
        $usuario = $candidatos->first(fn($user) =>
            Hash::check($credentials['usuario_password'], $user->usuario_password)
        );

        //Si no hay ninguno, credenciales inválidas
        if (! $usuario) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        //Autenticación satisfactoria: generar token
        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token'   => $token,
            'user'    => $usuario,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'Sesión cerrada'
        ];
    }
}

