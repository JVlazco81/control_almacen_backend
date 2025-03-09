<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->unsignedInteger('codigo')->primary(); // Asegurar que es UNSIGNED
            $table->string('descripcion_categoria', 100);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('categorias');
    }     
};
