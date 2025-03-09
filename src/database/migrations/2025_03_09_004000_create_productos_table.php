<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            // Campo auto-incremental y llave primaria
            $table->increments('id_producto');
            
            // Código de producto y descripción
            $table->integer('codigo');
            $table->string('descripcion_producto', 100);
            
            // Marca del producto
            $table->string('marca', 100)->nullable();
            
            // Cantidad inicial (se actualizará cuando se apruebe en inventario)
            $table->integer('cantidad')->default(0);
            
            // Llave foránea a unidades
            $table->integer('id_unidad')->unsigned();
            $table->decimal('precio', 10, 2);
            
            // Definir la llave foránea
            $table->foreign('id_unidad')
                  ->references('id_unidad')
                  ->on('unidades');
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
