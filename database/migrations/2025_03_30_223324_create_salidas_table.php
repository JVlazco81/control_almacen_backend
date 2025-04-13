<?php
// database/migrations/xxxx_xx_xx_create_salidas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalidasTable extends Migration
{
    public function up()
    {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id('id_salida');
            $table->unsignedBigInteger('id_departamento');
            $table->string('folio', 15);
            $table->smallInteger('salida_anual');
            $table->date('fecha_salida');
            $table->smallInteger('orden_compra');
            $table->timestamps();
        
            $table->foreign('id_departamento')->references('id_departamento')->on('departamentos')->onDelete('cascade');

            $table->softDeletes();
        });

    }

    public function down()
    {
        Schema::dropIfExists('salidas');
    }
};