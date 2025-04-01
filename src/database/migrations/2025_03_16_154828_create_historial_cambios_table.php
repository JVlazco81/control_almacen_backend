<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial_cambios', function (Blueprint $table) {
            $table->increments('id_historial');
            $table->string('tipo_auditado', 30); // Ej.: 'Producto', 'Entrada', 'Salida'
            $table->integer('id_auditado')->unsigned(); // ID del registro afectado
            $table->integer('id_usuario')->unsigned(); // Usuario que realizó la acción
            $table->string('accion', 20); // Ej.: 'actualizacion', 'eliminacion', 'insercion'
            $table->json('valor_anterior')->nullable(); // Estado previo de los campos modificados
            $table->json('valor_nuevo')->nullable(); // Estado posterior o NULL en caso de eliminación
            $table->dateTime('fecha');

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios');
                  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_cambios');
    }
};
