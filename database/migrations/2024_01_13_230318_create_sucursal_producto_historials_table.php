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
        Schema::create('sucursal_producto_historials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_producto");
            $table->string("fecha_vencimiento", 10)->default("0000-00-00");
            $table->string("lote", 20);
            $table->decimal("precio_entrada", 10, 3)->nullable();
            $table->decimal("precio_salida", 10 , 3)->nullable();
            $table->bigInteger("id_venta")->nullable();
            $table->bigInteger("id_compra")->nullable();
            $table->char("tipo_movimiento", 1);
            $table->integer("cantidad");
            $table->bigInteger("id_sucursal_origen")->nullable();
            $table->bigInteger("id_sucursal_destino")->nullable();
            $table->date("fecha_movimiento");
            $table->softDeletes();
            $table->timestamps();

            $table->index(['fecha_movimiento']);
            $table->index(['id_venta','id_compra']);
            $table->index(['id_producto','fecha_vencimiento','lote','precio_entrada','precio_salida'], "producto_full");
            $table->index(['id_sucursal_origen','id_sucursal_destino'], 'sucursales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursal_producto_historials');
    }
};
