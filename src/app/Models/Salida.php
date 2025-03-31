<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $table = 'salidas';
    protected $primaryKey = 'id_salida';

    protected $fillable = [
        'id_departamento',
        'folio',
        'salida_anual',
        'fecha_salida',
        'orden_compra'
    ];

    // Relación con Departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    // Relación con los detalles de salida (productos)
    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'id_salida');
    }
}
