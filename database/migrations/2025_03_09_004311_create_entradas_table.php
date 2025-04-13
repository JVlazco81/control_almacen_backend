<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradasTable extends Migration
{
    public function up()
    {
        Schema::create('entradas', function (Blueprint $table) {
            // Campo auto-incremental y llave primaria
            $table->increments('id_entrada');
            
            // Llave foránea a la tabla proveedores
            $table->integer('id_proveedor')->unsigned();
            
            // Campos de información de la entrada
            $table->string('folio', 15);
            $table->smallInteger('entrada_anual');
            $table->date('fecha_factura');
            $table->date('fecha_entrada');
            $table->text('nota')->nullable();
            
            // Definir la llave foránea
            $table->foreign('id_proveedor')
                  ->references('id_proveedor')
                  ->on('proveedores');

            $table->softDeletes();      
        });
    }

    public function down()
    {
        Schema::dropIfExists('entradas');
    }
}
