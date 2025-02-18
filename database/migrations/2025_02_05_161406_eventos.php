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
        Schema::create('eventos', function (Blueprint $table): void {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['conferencia', 'taller']);
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedBigInteger('ponente_id');
            $table->integer('capacidad_maxima');
            $table->timestamps();
            $table->foreign('ponente_id')->references('id')->on('ponentes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
