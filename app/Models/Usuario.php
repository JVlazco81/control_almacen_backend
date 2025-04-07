<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


class Usuario extends Authenticatable
{
    use HasFactory, HasApiTokens, SoftDeletes;
    // Definir la tabla y la llave primaria personalizada
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    public $timestamps = false;


    // Atributos asignables
    protected $fillable = [
        'id_rol',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'usuario_password',
    ];

    protected $hidden = [
        'usuario_password'
    ];

    protected $dates = ['deleted_at'];


    // Si la contraseña está almacenada con hash, puedes indicar el campo a utilizar
    public function getAuthPassword()
    {
        return $this->usuario_password;
    }

    // Relación con el modelo Role (opcional)
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
}