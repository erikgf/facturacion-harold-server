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
        Schema::create('ubigeo_peru_distritos', function (Blueprint $table) {
            $table->char("id", 6)->primary();
            $table->string("nombre", 45);
            $table->char("province_id", 4)->index();
            $table->char("department_id", 2)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubigeo_peru_distritos');
    }
};
