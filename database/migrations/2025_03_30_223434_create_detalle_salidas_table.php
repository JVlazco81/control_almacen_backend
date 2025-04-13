<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleSalidasTable extends Migration
{
    public function up()
    {
        Schema::create('detalle_salidas', function (Blueprint $table) {
            $table->id('id_detalle_salida');

            // Para la salida usamos bigInteger ya que en salidas usamos $table->id('id_salida')
            $table->unsignedBigInteger('id_salida');

            // Para producto usamos unsignedInteger para que coincida con la migración de productos
            $table->unsignedInteger('id_producto');

            $table->integer('cantidad');

            $table->foreign('id_salida')
                  ->references('id_salida')->on('salidas')
                  ->onDelete('cascade');

            $table->foreign('id_producto')
                  ->references('id_producto')->on('productos')
                  ->onDelete('cascade');

            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalle_salidas');
    }
}
