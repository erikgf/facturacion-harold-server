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
        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();
            $table->char("id_tipo_documento", 1);
            $table->string("numero_documento", 15)->nullable();
            $table->string("razon_social", 250);
            $table->string("direccion", 250)->nullable();
            $table->string("correo", 100)->nullable();
            $table->string("nombre_contacto", 250)->nullable();
            $table->string("celular_contacto", 15)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->fullText('razon_social');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedors');
    }
};
