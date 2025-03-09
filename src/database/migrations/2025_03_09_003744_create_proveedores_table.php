<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            // Campo auto-incremental y llave primaria
            $table->increments('id_proveedor');
            // Campo para el nombre del proveedor
            $table->string('nombre_proveedor', 100);
        });
    }

    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
}
