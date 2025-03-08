<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            // Campo auto-incremental y llave primaria
            $table->increments('id_rol');
            // Campo enum para el rol
            $table->enum('rol', ['almacenista', 'director']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}

