<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleSalida extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'detalle_salidas';
    protected $primaryKey = 'id_detalle_salida';
    public $timestamps = false;

    protected $fillable = ['id_salida', 'id_producto', 'cantidad'];

    protected $dates = ['deleted_at'];

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'id_salida');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
