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
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->boolean("es_menu_interfaz")->default(1);
            $table->string("titulo_interfaz", 50);
            $table->string("url", 50)->nullable();
            $table->string("icono_interfaz", 50);
            $table->bigInteger("id_permiso_padre")->nullable();
            $table->integer("orden")->nullable();
            $table->char("estado", 1)->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
