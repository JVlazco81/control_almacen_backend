<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';
    protected $primaryKey = 'id_departamento';
    public $timestamps = true;

    protected $fillable = ['nombre_departamento', 'nombre_encargado'];

     public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_departamento', 'id_departamento');
    }
}
