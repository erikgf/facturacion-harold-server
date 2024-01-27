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
        Schema::create('documento_electronico_detalles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_documento_electronico")->index();
            $table->bigInteger("id_producto")->index();
            $table->integer("item");
            $table->string("id_unidad_medida", 3);
            $table->decimal("cantidad_item", 10, 2);
            $table->string("descripcion_item", 350);
            $table->string("descripcion_detalle", 250)->nullable();
            $table->decimal("peso_bruto_total", 12, 2)->default(0.00);
            $table->decimal("peso_neto_total", 12, 2)->default(0.00);
            $table->decimal("precio_venta_unitario", 12, 2)->default(0.00);
            $table->decimal("subtotal", 12, 2)->default(0.00);
            $table->decimal("valor_venta_unitario", 14, 4)->default(0.0000);
            $table->decimal("valor_venta", 12, 2)->default(0.00);
            $table->decimal("total_igv", 12, 2)->default(0.00);
            $table->decimal("total_isc", 12, 2)->default(0.00);
            $table->char("id_tipo_afectacion", 2)->nullable();
            $table->char("id_codigo_precio", 2)->default('01')->nullable();
            $table->string("codigo_sunat", 15)->nullable();
            $table->string("codigo_interno", 15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_electronico_detalles');
    }
};
