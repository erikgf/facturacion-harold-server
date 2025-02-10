<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoResumenDiarioMasivoService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerarRDCRONCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generar_rd_cron';

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
        $status = "1";
        $fechaDesde = Carbon::now()->startOfWeek()->toDateString();
        $fechaHasta = Carbon::now()->endOfWeek()->toDateString();

        $res = (new DocumentoElectronicoResumenDiarioMasivoService)->procesar($fechaDesde, $fechaHasta, $status);
        $jsonRes = json_encode($res);

        $this->log("Generar RD CRON: {$jsonRes}");
        return Command::SUCCESS;
    }
}
