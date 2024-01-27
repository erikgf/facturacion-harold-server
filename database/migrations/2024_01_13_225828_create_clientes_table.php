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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->char("id_tipo_documento", 1);
            $table->string("numero_documento", 15)->nullable();
            $table->string("nombres", 300);
            $table->string("apellidos", 300)->nullable();
            $table->string("direccion", 400)->nullable();
            $table->string("correo", 50)->nullable();
            $table->char("sexo", 1)->nullable();
            $table->string("celular", 10)->nullable();
            $table->date("fecha_nacimiento")->nullable();
            $table->string("numero_contacto", 20)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->fullText('nombres');
            $table->fullText('direccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
