<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contacto_id');
            $table->unsignedBigInteger('custom_field_id');
            $table->text('value');
            $table->timestamps();

            $table->foreign('contacto_id')->references('id')->on('contactos')->onDelete('cascade');
            $table->foreign('custom_field_id')->references('id')->on('custom_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_field_values');
    }
}
