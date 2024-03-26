<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClocalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clocals', function (Blueprint $table) {
            $table->id();
            $table->string('empresa')->nullable();
            $table->string('id_pdf')->nullable();
            $table->string('contrato')->nullable();
            $table->string('codigo_contrato')->nullable();
            $table->string('tipo_orden_id')->nullable();
            $table->string('orden_servicio')->nullable();
            $table->string('desc_general_act')->nullable();
            $table->string('objeto')->nullable();
            $table->string('tiempo_ejecucion')->nullable();
            $table->string('fecha_inicio')->nullable();
            $table->string('fecha_recibo')->nullable();
            $table->string('hora_limite')->nullable();
            $table->json('tag')->nullable();
            $table->string('publicacion')->nullable();
            $table->string('estado')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clocals');
    }
}
