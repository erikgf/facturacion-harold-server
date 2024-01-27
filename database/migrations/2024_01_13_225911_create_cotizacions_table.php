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
        Schema::create('cotizacions', function (Blueprint $table) {
            $table->id();
            $table->char("id_tipo_comprobante", 2);
            $table->char("serie", 4);
            $table->integer("correlativo");
            $table->bigInteger("id_cliente");
            $table->string("razon_social_nombre", 300);
            $table->text("direccion_cliente");
            $table->string("correo_envio", 50);
            $table->string("numero_documento", 15)->nullable();
            $table->date("fecha_cotizacion");
            $table->char("id_tipo_moneda", 3);
            $table->decimal("subtotal", 10, 2);
            $table->decimal("igv", 10, 2);
            $table->decimal("total", 10, 2);
            $table->integer("condicion_dias_credito");
            $table->integer("condicion_dias_validez");
            $table->integer("condicion_dias_entrega");
            $table->decimal("condicion_delivery", 10 , 2);
            $table->string("cta_bcp", 50);
            $table->string("cta_bbva", 50);
            $table->string("cta_bcp_cci", 50);
            $table->string("cta_bbva_cci", 50);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacions');
    }
};
