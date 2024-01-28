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
        Schema::create('envio_comprobante_sunats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_comprobante_asociado")->index();
            $table->char("id_tipo_comprobante", 2)->index();
            $table->integer("item");
            $table->string("nombre_comprobante");
            $table->dateTime("fecha_hora_envio")->default('now');
            $table->string("ticket", 100)->nullable();
            $table->string("cdr_estado", 200)->nullable();
            $table->text("cdr_descripcion")->nullable();
            $table->text("hash_cdr")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio_comprobante_sunats');
    }
};
