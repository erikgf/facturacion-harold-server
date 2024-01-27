<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::disableQueryLog();

        $this->call([
            EmpresaFacturacionSeeder::class,
            MotivoNotaSunatSeeder::class,
            TipoAfectacionSunatSeeder::class,
            TipoOperacionSunatSeeder::class,
            UbigeoPeruDepartamentoSeeder::class,
            UbigeoPeruProvinciaSeeder::class,
            UbigeoPeruDistritoSeeder::class,
            SucursalSeeder::class,
            RolSeeder::class,
            PermisoSeeder::class,
            PermisoRolSeeder::class,
            MarcaSeeder::class,
            PresentacionSeeder::class,
            TipoCategoriaSeeder::class,
            CategoriaProductoSeeder::class,
            TipoComprobanteSeeder::class,
            TipoDocumentoSeeder::class,
            TipoMonedaSeeder::class,
            UnidadMedidaSeeder::class,
            ProveedorSeeder::class,
            ClienteSeeder::class,
            UsuarioSeeder::class,
            ProductoSeeder::class,
            CompraSeeder::class,
            VentaSeeder::class
        ]);
    }
}

