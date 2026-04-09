<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Modules\Inventory\Services\ProcurementService;

#[Signature('wms:procurement-scan')]
#[Description('Scan product velocity to calculate ABC curves and generate restock alerts')]
class WmsProcurementScan extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ProcurementService $service)
    {
        $this->info('Starting WMS Procurement Scan...');
        $service->scanAndGenerateRestockAlerts();
        $this->info('Scan completed. Check logs for alerts.');
    }
}
