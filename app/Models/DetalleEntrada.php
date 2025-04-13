<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleEntrada extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detalleE';
    protected $primaryKey = 'id_detalleE';
    public $timestamps = false;

    protected $fillable = ['id_entrada', 'id_producto', 'cantidad'];

    protected $dates = ['deleted_at'];

    // Relación con entrada (cada detalle pertenece a una entrada)
    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'id_entrada');
    }

    // Relación con productos (cada detalle pertenece a un producto)
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
