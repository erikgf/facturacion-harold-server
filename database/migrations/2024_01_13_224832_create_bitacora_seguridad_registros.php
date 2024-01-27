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
        Schema::create('bitacora_seguridad_registros', function (Blueprint $table) {
            $table->id();
            $table->string("nombre_tabla", 50);
            $table->bigInteger("id_registro");
            $table->dateTime("fecha_hora_registro")->default(now());
            $table->bigInteger("id_usuario_registro");
            $table->dateTime("fecha_hora_edicion")->nullable();
            $table->bigInteger("id_usuario_edicion")->nullable();
            $table->dateTime("fecha_hora_baja")->nullable();
            $table->bigInteger("id_usuario_baja")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_seguridad_registros');
    }
};
