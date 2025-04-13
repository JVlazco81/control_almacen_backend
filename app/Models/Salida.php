<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salida extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'salidas';
    protected $primaryKey = 'id_salida';

    protected $fillable = [
        'id_departamento',
        'folio',
        'salida_anual',
        'fecha_salida',
        'orden_compra'
    ];

    protected $dates = ['deleted_at'];

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
