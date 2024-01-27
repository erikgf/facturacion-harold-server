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
        Schema::create('documento_electronicos', function (Blueprint $table) {
            $table->id();
            $table->char("id_tipo_comprobante", 2);
            $table->char("serie", 4);
            $table->integer("correlativo");
            $table->char("id_tipo_afectacion", 4)->default("0101");
            $table->bigInteger("id_cliente")->index();
            $table->char("id_tipo_documento_cliente", 1);
            $table->string("numero_documento_cliente", 15);
            $table->string("descripcion_cliente", 400);
            $table->string("direccion_cliente", 400)->nullable();
            $table->char("codigo_ubigeo_cliente",6)->default("140101");
            $table->date("fecha_emision");
            $table->char("hora_emision", 5);
            $table->date("fecha_vencimiento");
            $table->char("id_tipo_moneda", 3)->default("PEN");
            $table->decimal("total_gravadas", 12, 2)->default(0.00);
            $table->decimal("total_inafectas", 12 , 2)->default(0.00);
            $table->decimal("total_exoneradas", 12 , 2)->default(0.00);
            $table->decimal("descuento_global", 12 , 2)->default(0.00);
            $table->decimal("descuento_global_igv", 14 , 4)->default(0.0000);
            $table->decimal("porcentaje_descuento", 6 , 3)->default(0.000);
            $table->decimal("importe_total", 12 , 2)->default(0.00);
            $table->decimal("importe_credito", 12 , 2)->default(0.00);
            $table->decimal("igv", 5 , 2);
            $table->decimal("total_igv", 12 , 2)->default(0.00);
            $table->decimal("total_isc", 12 , 2)->default(0.00);
            $table->decimal("total_otro_imp", 12 , 2)->default(0.00);
            $table->text("total_letras");
            $table->char("id_tipo_comprobante_modifica", 2)->nullable();
            $table->char("serie_comprobante_modifica", 4)->nullable();
            $table->integer("correlativo_comprobante_modifica")->nullable();
            $table->char("id_tipo_motivo_nota", 2)->nullable();
            $table->string("descripcion_motivo_nota", 250)->nullable();
            $table->text("observaciones")->nullable();
            $table->char("condicion_pago",1)->default('1')->comment("Donde 1 = CONTADO y 0 = CREDITO");
            $table->char("es_detraccion",1)->default(0);
            $table->char("enviar_a_sunat", 1)->default('0');
            $table->char("es_delivery", 1)->default('0');
            $table->char("fue_anulado_por_nota", 1)->default("1")->comment("solo se usa para diferneciar entre dado de baja por nota o por comunicacion de baja");
            $table->char("esta_anulado", 1)->default('0');
            $table->string("cdr_estado", 30)->nullable();
            $table->string("cdr_descripcion", 300)->nullable();
            $table->string("cdr_hash", 300)->nullable();
            $table->text("valor_firma")->nullable();
            $table->string("valor_resumen", 64)->nullable();
            $table->string("xml_filename", 300)->nullable();
            $table->string("cdr_filename", 300)->nullable();
            $table->char("fue_generado", 1)->default("0");
            $table->char("fue_firmado", 1)->default("0");
            $table->bigInteger("id_atencion")->nullable()->index()->comment("Se puede usar id_venta o id_comrpra o lo que sea.");
            $table->bigInteger("id_usuario_registro")->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['id_tipo_comprobante','serie','correlativo'], 'comprobante_numdoc');
            $table->index(['fecha_emision']);
            $table->index(['id_tipo_comprobante_modifica','serie_comprobante_modifica','correlativo_comprobante_modifica'], 'comprobante_modifica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_electronicos');
    }
};
