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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("numero_documento", 8);
            $table->string('nombres_apellidos', 350);
            $table->string("celular", 15)->nullable();
            $table->date("fecha_nacimiento")->nullable();
            $table->date("fecha_ingreso")->nullable();
            $table->integer("id_rol");
            $table->char("sexo", 1)->default('M');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean("acceso_sistema")->default(false);
            $table->char("estado_activo", 1)->default('A');
            $table->string('password');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
