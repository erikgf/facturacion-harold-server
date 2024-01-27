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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->char("id_tipo_comprobante", 2);
            $table->char("serie", 4);
            $table->integer("correlativo");
            $table->bigInteger("id_cliente");
            $table->char("tipo_pago", 1)->default("C");
            $table->decimal("monto_efectivo", 10, 2)->default(0.00);
            $table->decimal("monto_credito", 10 , 2)->default(0.00);
            $table->decimal("monto_tarjeta", 10, 2)->default(0.00);
            $table->decimal("monto_yape", 10 ,2)->default(0.00);
            $table->decimal("monto_plin", 10, 2)->default(0.00);
            $table->decimal("monto_transferencia", 10, 2)->default(0.00);
            $table->char("tipo_descuento", 1)->default("V");
            $table->decimal("valor_descuento", 10, 2)->default(0.00);
            $table->decimal("monto_descuento", 10 , 2)->default(0.00);
            $table->decimal("sub_total", 10 , 2);
            $table->decimal("monto_total_venta", 10 , 2);
            $table->date("fecha_venta");
            $table->time("hora_venta");
            $table->bigInteger("id_sucursal");
            $table->char("id_tipo_moneda", 3);
            $table->text("observaciones")->nullable();
            $table->bigInteger("id_usuario_registro");
            $table->softDeletes();
            $table->timestamps();

            $table->index(['id_tipo_comprobante','serie','correlativo']);
            $table->index(['id_cliente']);
            $table->index(['id_sucursal']);
            $table->index(['fecha_venta']);
            $table->index(['id_usuario_registro']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
