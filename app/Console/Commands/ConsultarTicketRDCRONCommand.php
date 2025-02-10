<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoResumenDiarioSUNATService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ConsultarTicketRDCRONCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consultar_ticket_rd_cron';

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
        $fechaHasta = Carbon::now()->endOfWeek()->toDateString();

        $rdSunatService = new DocumentoElectronicoResumenDiarioSUNATService;
        $datosParaConsultar = $rdSunatService->obtenerDatosParaConsultarTicketMasivo($fechaDesde, $fechaHasta);
        $res = $rdSunatService->consultarTickets($datosParaConsultar);

        $jsonRes = json_encode($res);
        $this->log("Consultar Ticket RD CRON: {$jsonRes}");

        return Command::SUCCESS;
    }
}
