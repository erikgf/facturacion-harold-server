<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoSUNATMasivoService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnviarFacturaCRONCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar_factura_cron';

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
        $res = (new DocumentoElectronicoSUNATMasivoService)->procesar($fecha, "01");
        $jsonRes = json_encode($res);

        $this->log("Enviar Factura CRON: {$jsonRes}");
        return Command::SUCCESS;
    }
}
