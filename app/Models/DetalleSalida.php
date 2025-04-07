<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    protected $table = 'detalle_salidas';
    protected $primaryKey = 'id_detalle_salida';
    protected $fillable = ['id_salida', 'id_producto', 'cantidad'];

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'id_salida');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
