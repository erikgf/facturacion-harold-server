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
        Schema::create('permiso_rols', function (Blueprint $table) {
            $table->bigInteger("id_permiso");
            $table->bigInteger("id_rol");
            $table->char("estado", 1)->default('A');
            $table->timestamps();
            $table->primary(['id_permiso', 'id_rol']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permiso_rols');
    }
};
