<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 


class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false; // Si no deseas usar created_at y updated_at, pero se seguirá usando deleted_at

    protected $fillable = ['codigo', 'descripcion_producto', 'marca', 'cantidad', 'id_unidad', 'precio'];

    // Especifica que 'deleted_at' es una fecha
    protected $dates = ['deleted_at'];

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

    // Relación con categorias (un producto pertenece a una categoría)
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'codigo');
    }
    public function entradas()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_producto');
    }
    
    public function detalleSalidas()
    {
        return $this->hasMany(DetalleSalida::class, 'id_producto');
    }    
}
