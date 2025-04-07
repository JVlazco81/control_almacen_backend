<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;


class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios (excluye los borrados lógicamente).
     */
    public function index()
    {
        $usuarios = Usuario::all(); 
        return response()->json($usuarios);
    }

    /**
     * Mostrar un usuario individual por su ID.
     */
    public function show($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            return response()->json($usuario);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    }

    /**
     * Registrar un nuevo usuario.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_rol'           => 'required|integer',
            'primer_nombre'    => 'required|string|max:20',
            'segundo_nombre'   => 'nullable|string|max:40',
            'primer_apellido'  => 'required|string|max:25',
            'segundo_apellido' => 'required|string|max:25',
            'usuario_password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/'
            ],
        ]);

        $usuario = Usuario::create([
            'id_rol'           => $validated['id_rol'],
            'primer_nombre'    => $validated['primer_nombre'],
            'segundo_nombre'   => $validated['segundo_nombre'] ?? null,
            'primer_apellido'  => $validated['primer_apellido'],
            'segundo_apellido' => $validated['segundo_apellido'],
            'usuario_password' => Hash::make($validated['usuario_password']),
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'usuario' => $usuario
        ], 201);
    }

     /**
     * Actualizar un usuario existente.
     */
    public function update(Request $request, $id)
    {
        try {
            $usuario = Usuario::findOrFail($id);

            $validated = $request->validate([
                'id_rol'           => [
                    'sometimes',
                    'required',
                    'exists:roles,id_rol',
                    // No permitir cambiar el último director a otro rol
                    function ($attr, $value, $fail) use ($usuario) {
                        if ($usuario->rol->rol === 'director' && $value != $usuario->id_rol) {
                            $total = Usuario::whereHas('rol', fn($q) => $q->where('rol', 'director'))->count();
                            if ($total <= 1) {
                                $fail('No puedes cambiar el rol de este usuario porque es el único director.');
                            }
                        }
                    },
                ],
                'primer_nombre'    => 'sometimes|required|string|max:20',
                'segundo_nombre'   => 'sometimes|nullable|string|max:40',
                'primer_apellido'  => 'sometimes|required|string|max:25',
                'segundo_apellido' => 'sometimes|required|string|max:25',
                'usuario_password' => [
                    'sometimes',
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/'
                ],
            ], [
                'usuario_password.regex' => 'La contraseña debe contener al menos una mayúscula y un carácter especial.'
            ]);

            // Actualizar campos simples
            foreach (['id_rol','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido'] as $field) {
                if (isset($validated[$field])) {
                    $usuario->{$field} = $validated[$field];
                }
            }

            // Actualizar contraseña si viene
            if (isset($validated['usuario_password'])) {
                $usuario->usuario_password = Hash::make($validated['usuario_password']);
            }

            $usuario->save();

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'usuario' => $usuario
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Borrado lógico de un usuario (Soft Delete).
     */
    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);

            // Si es director, verificar que no sea el único
            if ($usuario->rol->rol === 'director') {
                $total = Usuario::whereHas('rol', fn($q) => $q->where('rol', 'director'))->count();
                if ($total <= 1) {
                    return response()->json([
                        'error' => 'No puedes eliminar este usuario porque es el único director.'
                    ], Response::HTTP_FORBIDDEN);
                }
            }

            $usuario->delete(); // SoftDeletes: marca deleted_at

            return response()->json(['message' => 'Usuario eliminado correctamente'], Response::HTTP_OK);

        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }
    }
}
