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
        Schema::create('documento_electronico_comunicacion_bajas', function (Blueprint $table) {
            $table->id();
            $table->char("codigo", 2);
            $table->char("serie", 8);
            $table->integer("secuencia");
            $table->string("nombre_resumen", 30);
            $table->integer("numero_envios")->default(0);
            $table->string("ticket", 100)->nullable();
            $table->string("cdr_estado", 15)->nullable();
            $table->string("cdr_descripcion")->nullable();
            $table->string("hash_cdr")->nullable();
            $table->date("fecha_baja");
            $table->date("fecha_generacion");
            $table->char("enviar_a_sunat", 1)->default('0');
            $table->char("fue_generado", 1)->default('0');
            $table->char("fue_firmado", 1)->default('0');
            $table->string("valor_resumen")->nullable();
            $table->string("valor_firma")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_electronico_comunicacion_bajas');
    }
};
