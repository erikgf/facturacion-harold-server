<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoResumenDiarioSUNATService;
use Illuminate\Console\Command;

class ConsultarTicketRDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consultar_ticket_rd {desde} {hasta}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $datosParaConsultar = $rdSunatService->obtenerDatosParaConsultarTicketMasivo($fechaDesde, $fechaHasta);
        $res = $rdSunatService->consultarTickets($datosParaConsultar);

        $jsonRes = json_encode($res);
        $this->info("Datos Tickets Procesados: {$jsonRes}");

        return Command::SUCCESS;
    }
}
