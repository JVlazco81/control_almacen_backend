<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';
    protected $primaryKey = 'id_unidad';
    public $timestamps = false;

    protected $fillable = ['tipo_unidad'];

    // Relación con productos (una unidad puede estar en muchos productos)
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_unidad');
    }
}
