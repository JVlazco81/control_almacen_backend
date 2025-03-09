<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = ['codigo', 'descripcion_producto', 'marca', 'cantidad', 'id_unidad', 'precio'];

    // Relación con unidades (un producto tiene una unidad de medida)
    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    // Relación con detalleE (un producto puede estar en muchas entradas)
    public function detalles()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_producto');
    }
}
