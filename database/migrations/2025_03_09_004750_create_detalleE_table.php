<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleETable extends Migration
{
    public function up()
    {
        Schema::create('detalleE', function (Blueprint $table) {
            // Campo auto-incremental y llave primaria
            $table->increments('id_detalleE');
            
            // Llaves foráneas a entradas y productos
            $table->integer('id_entrada')->unsigned();
            $table->integer('id_producto')->unsigned();
            
            // Cantidad de productos en la entrada
            $table->integer('cantidad');
            
            // Definir llaves foráneas
            $table->foreign('id_entrada')
                  ->references('id_entrada')
                  ->on('entradas');
                  
            $table->foreign('id_producto')
                  ->references('id_producto')
                  ->on('productos');
            
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalleE');
    }
}
