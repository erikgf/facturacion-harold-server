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
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_venta");
            $table->integer("item");
            $table->bigInteger("id_producto");
            $table->string("fecha_vencimiento", 10)->default("0000-00-00");
            $table->string("lote", 20)->default("");
            $table->integer("cantidad");
            $table->string("descripcion_producto", 300);
            //$table->decimal("valor_unitario", 10, 3);
            //$table->decimal("monto_igv", 10, 3);
            $table->decimal("precio_venta_unitario", 10, 2);
            $table->decimal("subtotal", 10, 2);
            //$table->decimal("valor_venta", 10 , 3);
            $table->text("cadena_stock_producto");
            $table->decimal("costo_producto", 10 , 2);
            $table->char("id_unidad_medida", 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};
