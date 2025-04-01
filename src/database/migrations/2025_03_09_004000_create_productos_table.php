<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    public function up()
{
    Schema::create('productos', function (Blueprint $table) {
        $table->increments('id_producto');
        $table->unsignedInteger('codigo'); // Código del producto
        $table->string('descripcion_producto', 150);
        $table->string('marca', 100)->nullable();
        $table->integer('cantidad')->default(0);
        $table->unsignedInteger('id_unidad');
        $table->decimal('precio', 10, 2);

        // Definir las llaves foráneas
        $table->foreign('id_unidad')->references('id_unidad')->on('unidades');
        $table->foreign('codigo')->references('codigo')->on('categorias'); // Relación con categorías

        // Agregar columna para soft deletes
        $table->softDeletes(); // Esto crea la columna 'deleted_at'
    });
}

public function down()
{
    Schema::dropIfExists('productos');
}

}
