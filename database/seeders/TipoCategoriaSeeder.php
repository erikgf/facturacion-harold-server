<?php

namespace Database\Seeders;

use App\Models\TipoCategoria;
use Illuminate\Database\Seeder;

class TipoCategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        TipoCategoria::truncate();

        $registros = [
            ['nombre'=>'POLOS', 'descripcion'=>'POLOS'],
            ['nombre'=>'PANTALON', 'descripcion'=>'PANTALON'],
            ['nombre'=>'CAMISA', 'descripcion'=>'CAMISA'],
            ['nombre'=>'COLCHA', 'descripcion'=>'COLCHA'],
            ['nombre'=>'BVD', 'descripcion'=>'BVD'],
            ['nombre'=>'VESTIDOS', 'descripcion'=>'VESTIDOS'],
            ['nombre'=>'SABANA', 'descripcion'=>'SABANA'],
            ['nombre'=>'MEDIAS', 'descripcion'=>'MEDIAS'],
            ['nombre'=>'BERMUDA', 'descripcion'=>'BERMUDA'],
            ['nombre'=>'SHORTS', 'descripcion'=>'SHORTS'],
            ['nombre'=>'TRUZA', 'descripcion'=>'TRUZA'],
            ['nombre'=>'PAÑAL', 'descripcion'=>'PAÑAL'],
            ['nombre'=>'MITONES', 'descripcion'=>'MITONES'],
            ['nombre'=>'AJUAR', 'descripcion'=>'AJUAR'],
            ['nombre'=>'GORRO', 'descripcion'=>'GORRO'],
            ['nombre'=>'BEBECRECE', 'descripcion'=>'BEBECRECE'],
            ['nombre'=>'OVEROL', 'descripcion'=>'OVEROL'],
            ['nombre'=>'BODY', 'descripcion'=>'BODY'],
            ['nombre'=>'JAMPER', 'descripcion'=>'JAMPER'],
            ['nombre'=>'BABERO', 'descripcion'=>'BABERO'],
            ['nombre'=>'TOALLA', 'descripcion'=>'TOALLA'],
            ['nombre'=>'CONJUNTO', 'descripcion'=>'CONJUNTO'],
            ['nombre'=>'BOLERO', 'descripcion'=>'BOLERO'],
            ['nombre'=>'CHOMPA', 'descripcion'=>'CHOMPA'],
            ['nombre'=>'POLERA', 'descripcion'=>'POLERA'],
            ['nombre'=>'LEGGIN', 'descripcion'=>'LEGGIN'],
            ['nombre'=>'JOGGERS', 'descripcion'=>''],
            ['nombre'=>'BATITAS', 'descripcion'=>''],
            ['nombre'=>'SOMBREROS', 'descripcion'=>''],
            ['nombre'=>'PORTABEBE', 'descripcion'=>''],
            ['nombre'=>'BANDANAS', 'descripcion'=>''],
        ];

        foreach ($registros as $key => $reg) {
            TipoCategoria::create($reg);
        }
    }
}
