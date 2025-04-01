<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios';
    protected $primaryKey = 'id_historial';
    public $timestamps = false;

    protected $fillable = [
        'tipo_auditado',
        'id_auditado',
        'id_usuario',
        'accion',
        'valor_anterior',
        'valor_nuevo',
        'fecha',
    ];

    /**
     * Relación con el usuario que realizó la acción.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Relación polimórfica para la entidad auditada.
     *
     * Usamos morphTo y especificamos los campos personalizados:
     * - tipo_auditado: indica el tipo de entidad (Producto, Entrada, Salida, etc.)
     * - id_auditado: el id del registro afectado
     */
    public function auditable()
    {
        return $this->morphTo(null, 'tipo_auditado', 'id_auditado');
    }
}

