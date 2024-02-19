<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNumerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('numeros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('numero');
            $table->string('id_telefono');
            $table->unsignedBigInteger('aplicacion_id')->nullable();
            $table->string('calidad');
            $table->timestamps();

            // Establece la columna `aplicacion_id` como clave foránea
            $table->foreign('aplicacion_id')->references('id')->on('aplicaciones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('numeros');
    }
}
