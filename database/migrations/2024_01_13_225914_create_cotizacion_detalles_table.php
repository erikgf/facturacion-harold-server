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
        Schema::create('cotizacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_cotizacion");
            $table->integer("item");
            $table->bigInteger("id_producto");
            $table->string("fecha_vencimiento", 10)->default("0000-00-00");
            $table->string("lote", 20)->default("");
            $table->char("id_unidad_medida", 3);
            $table->string("descripcion", 300);
            $table->bigInteger("id_marca")->nullable();
            $table->integer("cantidad");
            $table->decimal("valor_unitario", 10, 2);
            $table->decimal("precio_unitario", 10, 2);
            $table->decimal("monto_igv", 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_detalles');
    }
};
