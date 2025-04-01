<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    // nombre de la tabla
    protected $table = 'roles';

    // Definir la llave primaria
    protected $primaryKey = 'id_rol';

    // Se deshabilita el timestamp porque la tabla no tiene
    public $timestamps = false;

    // Atributos asignables
    protected $fillable = [
        'rol',
    ];

    // Relación con el modelo Usuario
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }
}

