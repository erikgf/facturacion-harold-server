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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->char("id_tipo_comprobante", 2);
            $table->string("numero_comprobante", 20);
            $table->integer("id_sucursal");
            $table->date("fecha_compra");
            $table->time("hora_compra");
            $table->text("observaciones")->nullable();
            $table->text("guias_remision")->nullable();
            $table->bigInteger("id_proveedor");
            $table->char("tipo_pago", 1);
            $table->char("tipo_tarjeta", 1)->nullable();
            $table->decimal("importe_total", 10, 2);
            $table->bigInteger("id_usuario_registro");
            $table->softDeletes();
            $table->timestamps();

            $table->index(['id_proveedor']);
            $table->index(['fecha_compra']);
            $table->index(['id_sucursal']);
            $table->index(['id_usuario_registro']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
