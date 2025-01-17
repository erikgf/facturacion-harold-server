<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoResumenDiarioSUNATService;
use Illuminate\Console\Command;

class EnviarRDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar_rd {desde} {hasta}';

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
        $fechaDesde = $this->argument('desde') ?? "";
        $fechaHasta = $this->argument("hasta") ?? "";

        if (!$fechaDesde){
            $this->error("Fecha DESDE no enviado.");
            return Command::FAILURE;
        }

        if (!$fechaHasta){
            $this->error("Fecha HASTA no enviado.");
            return Command::FAILURE;
        }

        $rdSunatService = new DocumentoElectronicoResumenDiarioSUNATService;
        $datosParaEnviar = $rdSunatService->obtenerDatosParaEnviarMasivo($fechaDesde, $fechaHasta);
        $res = $rdSunatService->enviarComprobantes($datosParaEnviar);

        $jsonRes = json_encode($res);
        $this->info("Datos Procesados: {$jsonRes}");

        return Command::SUCCESS;
    }
}
