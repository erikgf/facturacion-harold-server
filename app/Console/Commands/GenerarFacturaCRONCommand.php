<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoXMLMasivoService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarFacturaCRONCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generar_factura_cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fecha = Carbon::now()->yesterday();
        $res = (new DocumentoElectronicoXMLMasivoService)->procesar($fecha, "01");
        $jsonRes = json_encode($res);

        $this->log("Generar Factura CRON: {$jsonRes}");
        return Command::SUCCESS;
    }
}
