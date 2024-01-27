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
        Schema::create('venta_creditos', function (Blueprint $table) {
            $table->id();
            $table->decimal("monto", 10, 2);
            $table->boolean("tipo_deuda");
            $table->decimal("pendiente", 10 ,2);
            $table->date("fecha_registro");
            $table->bigInteger("id_venta");
            $table->text("observaciones")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_creditos');
    }
};
