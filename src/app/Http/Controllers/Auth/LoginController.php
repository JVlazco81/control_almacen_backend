<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = Usuario::where('primer_nombre', $request->primer_nombre)
               ->where('primer_apellido', $request->primer_apellido)
               ->first();

        if (!$user || $user->usuario_password !== $request->usuario_password) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ])->header('Content-Type', 'application/json; charset=UTF-8');

    }

    public function logout(Request $request)
    {
        //$request->user()->tokens()->delete();
        $request->user()->currentAccessToken()->delete();


        return [
            'message' => 'Sesión cerrada'
        ];
    }
}

