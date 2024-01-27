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
        Schema::create('compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_compra");
            $table->integer("item");
            $table->decimal("precio_unitario", 10, 2);
            $table->integer("cantidad");
            $table->bigInteger("id_producto");
            $table->string("fecha_vencimiento", 10)->default("0000-00-00");
            $table->string("lote", 20)->default("");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compra_detalles');
    }
};
