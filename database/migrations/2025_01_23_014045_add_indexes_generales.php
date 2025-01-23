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
        Schema::table('categoria_productos', function (Blueprint $table) {
            $table->index("id_tipo_categoria");
            $table->index("deleted_at");
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->index("id_tipo_documento");
            $table->index("numero_documento");
            $table->index("deleted_at");
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->index("id_tipo_comprobante");
            $table->index("numero_comprobante");
            $table->index("deleted_at");
        });

        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->index("id_compra");
            $table->index("id_producto");
        });

        Schema::table('documento_electronicos', function (Blueprint $table) {
            $table->index("cdr_estado");
            $table->index("deleted_at");
        });

        Schema::table('documento_electronico_detalles', function (Blueprint $table) {
            $table->index("id_unidad_medida");
        });

        Schema::table('empresa_facturacions', function (Blueprint $table) {
            $table->index("deleted_at");
        });

        Schema::table('permisos', function (Blueprint $table) {
            $table->index("id_permiso_padre");
            $table->index("es_menu_interfaz");
            $table->index("estado");
        });

        Schema::table('permiso_rols', function (Blueprint $table) {
            $table->index("estado");
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->index("id_unidad_medida");
            $table->index("id_presentacion");
            $table->index("id_marca");
            $table->index("id_categoria_producto");
            $table->index("deleted_at");
        });

        Schema::table('proveedors', function (Blueprint $table) {
            $table->index("id_tipo_documento");
            $table->index("numero_documento");
            $table->index("deleted_at");
        });

        Schema::table('serie_documentos', function (Blueprint $table) {
            $table->index("id_tipo_comprobante");
            $table->index("serie");
            $table->index("deleted_at");
        });

        Schema::table('sucursals', function (Blueprint $table) {
            $table->index("deleted_at");
        });

        Schema::table('sucursal_productos', function (Blueprint $table) {
            $table->index("id_sucursal");
            $table->index("deleted_at");
        });

        Schema::table('sucursal_producto_historials', function (Blueprint $table) {
            $table->index("tipo_movimiento");
            $table->index("deleted_at");
        });


        Schema::table('tipo_categorias', function (Blueprint $table) {
            $table->index("deleted_at");
        });

        Schema::table('tipo_documentos', function (Blueprint $table) {
            $table->index("deleted_at");
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index("acceso_sistema");
            $table->index("estado_activo");
            $table->index("deleted_at");
        });

        Schema::table('ventas', function (Blueprint $table) { //Tudip pl
            $table->index("deleted_at");
        });

        Schema::table('venta_creditos', function (Blueprint $table) { //Tudip pl
            $table->index("id_venta");
            $table->index("deleted_at");
        });

        Schema::table('venta_detalles', function (Blueprint $table) { //Tudip pl
            $table->index("id_venta");
            $table->index("id_producto");
            $table->index("id_unidad_medida");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoria_productos', function (Blueprint $table) {
            $table->dropIndex("id_tipo_categoria");
            $table->dropIndex("deleted_at");
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex("id_tipo_documento");
            $table->dropIndex("numero_documento");
            $table->dropIndex("deleted_at");
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropIndex("id_tipo_comprobante");
            $table->dropIndex("numero_comprobante");
            $table->dropIndex("deleted_at");
        });

        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->dropIndex("id_compra");
            $table->dropIndex("id_producto");
        });

        Schema::table('documento_electronicos', function (Blueprint $table) {
            $table->dropIndex("cdr_estado");
            $table->dropIndex("deleted_at");
        });

        Schema::table('documento_electronico_detalles', function (Blueprint $table) {
            $table->dropIndex("id_unidad_medida");
        });

        Schema::table('empresa_facturacions', function (Blueprint $table) {
            $table->dropIndex("deleted_at");
        });

        Schema::table('permisos', function (Blueprint $table) {
            $table->dropIndex("id_permiso_padre");
            $table->dropIndex("es_menu_interfaz");
            $table->dropIndex("estado");
        });

        Schema::table('permiso_rols', function (Blueprint $table) {
            $table->dropIndex("estado");
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex("id_unidad_medida");
            $table->dropIndex("id_presentacion");
            $table->dropIndex("id_marca");
            $table->dropIndex("id_categoria_producto");
            $table->dropIndex("deleted_at");
        });

        Schema::table('proveedors', function (Blueprint $table) {
            $table->dropIndex("id_tipo_documento");
            $table->dropIndex("numero_documento");
            $table->dropIndex("deleted_at");
        });

        Schema::table('serie_documentos', function (Blueprint $table) {
            $table->dropIndex("id_tipo_comprobante");
            $table->dropIndex("serie");
            $table->dropIndex("deleted_at");
        });

        Schema::table('sucursal_productos', function (Blueprint $table) {
            $table->dropIndex("id_sucursal");
            $table->dropIndex("deleted_at");
        });

        Schema::table('sucursal_producto_historials', function (Blueprint $table) {
            $table->dropIndex("tipo_movimiento");
            $table->dropIndex("deleted_at");
        });

        Schema::table('tipo_categorias', function (Blueprint $table) {
            $table->dropIndex("deleted_at");
        });

        Schema::table('tipo_documentos', function (Blueprint $table) {
            $table->dropIndex("deleted_at");
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex("acceso_sistema");
            $table->dropIndex("estado_activo");
            $table->dropIndex("deleted_at");
        });

        Schema::table('ventas', function (Blueprint $table) { //Tudip pl
            $table->dropIndex("deleted_at");
        });

        Schema::table('venta_creditos', function (Blueprint $table) { //Tudip pl
            $table->dropIndex("id_venta");
            $table->dropIndex("deleted_at");
        });

        Schema::table('venta_detalles', function (Blueprint $table) { //Tudip pl
            $table->dropIndex("id_venta");
            $table->dropIndex("id_producto");
            $table->dropIndex("id_unidad_medida");
        });

    }
};
