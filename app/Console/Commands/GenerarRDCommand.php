<?php

namespace App\Console\Commands;

use App\Services\DocumentoElectronicoResumenDiarioMasivoService;
use Illuminate\Console\Command;

class GenerarRDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generar_rd {desde} {hasta} {status?}';

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

        $status = $this->argument("status") ?? "1";

        if (!$fechaDesde){
            $this->error("Fecha DESDE no enviado.");
            return Command::FAILURE;
        }

        if (!$fechaHasta){
            $this->error("Fecha HASTA no enviado.");
            return Command::FAILURE;
        }

        $res = (new DocumentoElectronicoResumenDiarioMasivoService)->procesar($fechaDesde, $fechaHasta, $status);
        $jsonRes = json_encode($res);

        $this->info("Fechas Procesadas: {$jsonRes}");

        return Command::SUCCESS;
    }
}
