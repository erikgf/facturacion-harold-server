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
        Schema::create('documento_electronico_guias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_documento_electronico")->index();
            $table->char("id_motivo_traslado", 2)->nullable();
            $table->string("motivo_traslado", 100)->nullable();
            $table->char("id_tipo_transporte", 2)->nullable();
            $table->date("fecha_inicio_traslado")->nullable();
            $table->string("placa_transportista", 20)->nullable();
            $table->decimal("total_peso_bruto", 10, 2)->default(0.00);
            $table->decimal("total_peso_neto", 10, 2)->default(0.00);
            $table->decimal("costo_kg_neto", 10, 2)->default(0.00);
            $table->decimal("total_gastos", 10, 2)->default(0.00);
            $table->integer("numero_paquetes")->default(1);
            $table->char("id_tipo_documento_transportista", 1)->nullable();
            $table->string("numero_documento_transportista", 15)->nullable();
            $table->string("razon_social_transportista", 300)->nullable();
            $table->char("id_tipo_documento_conductor", 1)->nullable();
            $table->string("numero_documento_conductor", 15)->nullable();
            $table->string("nombre_completo_conductor", 400)->nullable();
            $table->char("codigo_ubigeo_partida", 6)->nullable();
            $table->text("direccion_punto_partida")->nullable();
            $table->char("codigo_ubigeo_llegada", 6)->nullable();
            $table->text("direccion_punto_llegada")->nullable();
            $table->string("orden_compra", 200)->nullable();
            $table->string("placa_vehiculo", 200)->nullable();
            $table->string("descripcion_condicion_pago", 200)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_electronico_guias');
    }
};
