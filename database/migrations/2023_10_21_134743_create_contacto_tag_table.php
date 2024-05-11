<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactoTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacto_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contacto_id')
                ->constrained('contactos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->constrained('tags')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacto_tag');
    }
}
