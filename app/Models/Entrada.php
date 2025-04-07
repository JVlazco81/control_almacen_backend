<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entradas';
    protected $primaryKey = 'id_entrada';
    public $timestamps = false;

    protected $fillable = ['id_proveedor', 'folio', 'entrada_anual', 'fecha_factura', 'fecha_entrada', 'nota'];

    // Relación con proveedor (una entrada pertenece a un proveedor)
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    // Relación con detalleE (una entrada tiene muchos detalles)
    public function detalles()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_entrada');
    }
}
