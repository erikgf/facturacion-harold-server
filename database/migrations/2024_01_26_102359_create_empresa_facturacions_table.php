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
        Schema::create('empresa_facturacions', function (Blueprint $table) {
            $table->id();
            $table->string("nro_documento_empresa", 15);
            $table->string("tipo_documento_empresa", 6);
            $table->string("nombre_comercial_empresa", 250);
            $table->string("razon_social_impresiones_empresa", 350);
            $table->string("razon_social_empresa", 350);
            $table->string("contacto_empresa", 200)->nullable();
            $table->string("codigo_ubigeo_empresa", 6);
            $table->text("direccion_empresa");
            $table->text("direccion2_empresa")->nullable();
            $table->string("departamento_empresa", 120);
            $table->string("provincia_empresa", 200);
            $table->string("distrito_empresa", 200);
            $table->string("telefono_empresa", 50);
            $table->string("telefono2_empresa", 50)->nullable();
            $table->string("urbanizacion_empresa", 350)->nullable();
            $table->string("resolucion_emision_empresa", 50);
            $table->char("codigo_pais_empresa", 2);

            $table->string("razon_social_cotizacion_empresa", 350);
            $table->text("direccion_cotizacion_empresa");
            $table->string("telefono_cotizacion_empresa", 50);

            $table->char("emisor_ruc", 11);
            $table->string("emisor_usuario_sol", 20);
            $table->string("emisor_pass_sol", 50);
            $table->char("modo_proceso_emision", 1)->default('3')->comment("1: PRODUCCION", "3: BETA");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_facturacions');
    }
};
