<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoResumenDiarioSUNATService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnviarRDCRONCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar_rd_cron';

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

        $fechaDesde = Carbon::now()->startOfWeek()->toDateString();
        $fechaHasta = "2025-02-05";//Carbon::now()->endOfWeek()->toDateString();

        $rdSunatService = new DocumentoElectronicoResumenDiarioSUNATService;
        $datosParaEnviar = $rdSunatService->obtenerDatosParaEnviarMasivo($fechaDesde, $fechaHasta);
        $res = $rdSunatService->enviarComprobantes($datosParaEnviar);

        $jsonRes = json_encode($res);
        $this->log("Enviar RD CRON: {$jsonRes}");

        return Command::SUCCESS;
    }
}
