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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string("codigo_generado", 20);
            $table->char("empresa_especial", 3);
            $table->string("tallas", 50)->nullable();
            $table->string("nombre", 200);
            $table->text("descripcion")->nullable();
            $table->decimal("precio_unitario", 10, 2)->default(0.00);
            $table->string("id_unidad_medida", 3)->default('NIU');
            $table->bigInteger("id_presentacion")->nullable();
            $table->bigInteger("id_marca")->nullable();
            $table->bigInteger("id_categoria_producto");
            $table->integer("numero_imagen_principal")->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->fullText('nombre');
            $table->index("codigo_generado");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
