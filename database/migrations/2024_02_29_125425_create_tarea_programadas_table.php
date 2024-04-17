<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTareaProgramadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarea_programadas', function (Blueprint $table) {
            $table->id();
            $table->string('token_app');
            $table->string('phone_id');
            $table->string('numeros');
            $table->json('payload');
            $table->longText('body');
            $table->binary('messageData');
            $table->string('status', 15);
            $table->string('fecha_programada');
            $table->json('tag');
            $table->string('distintivo');
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
        Schema::dropIfExists('tarea_programadas');
    }
}
