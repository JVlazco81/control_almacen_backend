<?php
// database/migrations/xxxx_xx_xx_create_departamentos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentosTable extends Migration
{
    public function up()
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id('id_departamento'); // ✅ Esto es lo importante
            $table->string('nombre_departamento', 55);
            $table->string('nombre_encargado', 55);
            $table->timestamps();
        });
        
        
    }

    public function down()
    {
        Schema::dropIfExists('departamentos');
    }
};
