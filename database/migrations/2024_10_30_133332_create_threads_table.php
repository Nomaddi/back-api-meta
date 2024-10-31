<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id')->unique(); // Número de WhatsApp del usuario, único
            $table->string('thread_id'); // ID del hilo de conversación
            $table->unsignedBigInteger('bot_id')->nullable(); // ID de referencia al bot, permite valores nulos
            $table->timestamps(); // created_at y updated_at

            //$table->foreign('bot_id')->references('id')->on('bots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
