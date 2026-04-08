<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Services\ThermalPrinterService;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    protected $printerService;

    public function __construct(ThermalPrinterService $printerService)
    {
        $this->printerService = $printerService;
    }

    public function test()
    {
        $result = $this->printerService->testConnection();
        return response()->json($result);
    }
}
