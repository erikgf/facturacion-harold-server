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
        Schema::create('documento_electronico_resumen_diario_detalles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_documento_electronico_resumen_diario")->index("id_de_rd");
            $table->integer("item");
            $table->char("id_tipo_comprobante", 2);
            $table->char("serie_comprobante", 4);
            $table->integer("correlativo_comprobante");
            $table->char("id_tipo_documento_cliente", 1);
            $table->string("numero_documento_cliente", 15);
            $table->char("id_tipo_comprobante_modificado", 2)->nullable();
            $table->char("serie_comprobante_modificado", 4)->nullable();
            $table->integer("correlativo_comprobante_modificado")->nullable();
            $table->smallInteger("status")->default(1)->comment("1: ADD, 2: MOD, 3: ANULAR");
            $table->char("id_tipo_moneda");
            $table->decimal("importe_gravadas", 12,2);
            $table->decimal("importe_exoneradas", 12,2)->default(0.00);
            $table->decimal("importe_inafectas", 12,2)->default(0.00);
            $table->decimal("importe_exportacion", 12,2)->default(0.00);
            $table->decimal("importe_gratuitas", 12,2)->default(0.00);
            $table->decimal("importe_otros", 12,2)->default(0.00);
            $table->decimal("importe_igv", 12,2);
            $table->decimal("importe_isc", 12,2)->default(0.00);
            $table->decimal("importe_total", 12,2);
            $table->timestamps();

            $table->index(["id_tipo_comprobante", "serie_comprobante", "correlativo_comprobante"], "comprobante");
            $table->index(["id_tipo_comprobante_modificado", "serie_comprobante_modificado", "correlativo_comprobante_modificado"], "comprobante_mod");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_electronico_resumen_diario_detalles');
    }
};
