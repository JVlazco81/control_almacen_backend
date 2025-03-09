<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['codigo', 'descripcion_categoria'];

    // Relación con productos (una categoría puede tener muchos productos)
    public function productos()
    {
        return $this->hasMany(Producto::class, 'codigo');
    }
}
