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
        Schema::create('sucursal_productos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_sucursal");
            $table->bigInteger("id_producto");
            $table->string("fecha_vencimiento", 10)->default("0000-00-00");
            $table->string("lote", 20);
            $table->decimal("precio_entrada", 10, 2);
            $table->integer("stock");
            $table->softDeletes();
            $table->timestamps();

            $table->index(['id_producto','fecha_vencimiento','lote','precio_entrada'], 'producto_full');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursal_productos');
    }
};
