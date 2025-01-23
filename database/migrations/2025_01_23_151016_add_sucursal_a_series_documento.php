<?php

use App\Models\Sucursal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('serie_documentos', function (Blueprint $table) {
            $table->integer("id_sucursal")->index();
        });

        $sucursal = Sucursal::query()->first("id");
        if ($sucursal){
            DB::table("serie_documentos")->update(["id_sucursal" => $sucursal->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serie_documentos', function (Blueprint $table) {
            $table->dropIndex("id_sucursal");
            $table->dropColumn("id_sucursal");
        });
    }
};
