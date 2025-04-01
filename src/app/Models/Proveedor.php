<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;

    protected $fillable = ['nombre_proveedor'];

    // Relación con entradas (un proveedor tiene muchas entradas)
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'id_proveedor');
    }
}
