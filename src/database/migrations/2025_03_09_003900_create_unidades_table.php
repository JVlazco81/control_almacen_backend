<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadesTable extends Migration
{
    public function up()
    {
        Schema::create('unidades', function (Blueprint $table) {
            // Campo auto-incremental y llave primaria
            $table->increments('id_unidad');
            
            // Tipo de unidad (ejemplo: PZA, KG, LITRO)
            $table->string('tipo_unidad', 20);
        });
    }

    public function down()
    {
        Schema::dropIfExists('unidades');
    }
}
