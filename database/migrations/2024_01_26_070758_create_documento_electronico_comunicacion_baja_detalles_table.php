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
        Schema::create('documento_electronico_comunicacion_baja_detalles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_documento_electronico_comunicacion_baja")->index("id_de_cb");
            $table->integer("item");
            $table->char("id_tipo_comprobante", 2);
            $table->char("serie_comprobante", 4);
            $table->integer("correlativo_comprobante");
            $table->string("motivo", 300);
            $table->timestamps();

            $table->index(["id_tipo_comprobante", "serie_comprobante", "correlativo_comprobante"], "comprobante");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_electronico_comunicacion_baja_detalles');
    }
};
