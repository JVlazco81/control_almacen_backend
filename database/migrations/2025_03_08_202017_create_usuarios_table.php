<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            // Campo auto-incremental y llave primaria
            $table->increments('id_usuario');
            
            // Llave foránea a la tabla roles
            $table->integer('id_rol')->unsigned();
            
            // Campos de nombres y apellidos
            $table->string('primer_nombre', 20);
            $table->string('segundo_nombre', 40)->nullable();
            $table->string('primer_apellido', 25);
            $table->string('segundo_apellido', 25);
            
            // Campo para la contraseña
            $table->string('usuario_password', 255);
            $table->softDeletes();

            // Definir la llave foránea
            $table->foreign('id_rol')
                  ->references('id_rol')
                  ->on('roles');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}

