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
        Schema::create('documento_electronico_cuotas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_documento_electronico")->index();
            $table->string("numero_cuota", 20);
            $table->date("fecha_vencimiento");
            $table->decimal("monto_cuota", 10 ,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_electronico_cuotas');
    }
};
