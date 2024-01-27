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
        Schema::create('motivo_nota_sunats', function (Blueprint $table) {
            $table->char("id_tipo_motivo", 2);
            $table->char("id_tipo_nota", 2);
            $table->string("descripcion", 50);
            $table->timestamps();

            $table->primary(["id_tipo_motivo", "id_tipo_nota"], "id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motivo_nota_sunats');
    }
};
