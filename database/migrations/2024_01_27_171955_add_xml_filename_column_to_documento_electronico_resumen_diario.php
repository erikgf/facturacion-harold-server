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
        Schema::table('documento_electronico_resumen_diarios', function (Blueprint $table) {
            $table->string('xml_filename', 300)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_electronico_resumen_diarios', function (Blueprint $table) {
            $table->dropColumn('xml_filename');
        });
    }
};
