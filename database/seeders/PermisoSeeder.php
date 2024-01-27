<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Permiso::truncate();

        $registros = [
            ['es_menu_interfaz'=>'0','titulo_interfaz'=>'Mantenimientos','url'=>NULL,'icono_interfaz'=>'edit','id_permiso_padre'=>NULL,'orden'=>NULL,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Clientes','url'=>'cliente.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>0,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Proveedor','url'=>'proveedor.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>1,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Personal','url'=>'personal.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>2,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Sucursal','url'=>'sucursal.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>3,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Comisionistas','url'=>'comisionista.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>4,'estado'=>'I'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Cargos','url'=>'cargo.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>5,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Productos','url'=>'producto.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>9,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Tipo de Cat. Prod.','url'=>'tipo.categoria.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>8,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Categoría de Productos','url'=>'categoria.producto.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>8,'estado'=>'A'],
            ['es_menu_interfaz'=>'0','titulo_interfaz'=>'Transacciones','url'=>NULL,'icono_interfaz'=>'file-o','id_permiso_padre'=>NULL,'orden'=>NULL,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Generar Descuentos','url'=>'descuento.vista.php','icono_interfaz'=>'','id_permiso_padre'=>11,'orden'=>0,'estado'=>'I'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Ventas','url'=>'ventas.vista.php','icono_interfaz'=>'','id_permiso_padre'=>11,'orden'=>1,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Compras','url'=>'compras.vista.php','icono_interfaz'=>'','id_permiso_padre'=>11,'orden'=>2,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Almacén','url'=>'almacen.vista.php','icono_interfaz'=>'','id_permiso_padre'=>11,'orden'=>3,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Catálogo','url'=>'principal.vista.php','icono_interfaz'=>'file','id_permiso_padre'=>NULL,'orden'=>NULL,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Permisos','url'=>'permisos.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>10,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Roles','url'=>'rol.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>11,'estado'=>'A'],
            ['es_menu_interfaz'=>'0','titulo_interfaz'=>'Reportes','url'=>NULL,'icono_interfaz'=>'edit','id_permiso_padre'=>NULL,'orden'=>NULL,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Reporte de Ventas','url'=>'reporte.ventas.vista.php','icono_interfaz'=>'','id_permiso_padre'=>19,'orden'=>0,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Reporte de Stock','url'=>'reporte.stock.vista.php','icono_interfaz'=>'','id_permiso_padre'=>19,'orden'=>1,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Producto más vendido','url'=>'reporte.mas.vendido.vista.php','icono_interfaz'=>'','id_permiso_padre'=>19,'orden'=>2,'estado'=>'A'],
            ['es_menu_interfaz'=>'0','titulo_interfaz'=>'Facturación','url'=>NULL,'icono_interfaz'=>'money','id_permiso_padre'=>NULL,'orden'=>NULL,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Gestión Comprobantes','url'=>'fact.comprobantes.vista.php','icono_interfaz'=>'','id_permiso_padre'=>23,'orden'=>0,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Marcas','url'=>'marca.vista.php','icono_interfaz'=>'','id_permiso_padre'=>1,'orden'=>6,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Cotizaciones','url'=>'cotizaciones.vista.php','icono_interfaz'=>'','id_permiso_padre'=>11,'orden'=>4,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Reporte Kardex','url'=>'reporte.kardex.vista.php','icono_interfaz'=>'','id_permiso_padre'=>19,'orden'=>3,'estado'=>'A'],
            ['es_menu_interfaz'=>'1','titulo_interfaz'=>'Pagos Ventas','url'=>'pagos.ventas.vista.php','icono_interfaz'=>'','id_permiso_padre'=>11,'orden'=>5,'estado'=>'A'],
        ];

        foreach ($registros as $key => $reg) {
            Permiso::create($reg);
        }
    }
}
