<?php

namespace App\Modules\Fiscal\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fiscal\Services\NfceEngineService;
use Illuminate\Http\Request;

class TestEngineController extends Controller
{
    protected $engine;

    public function __construct(NfceEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function sandbox()
    {
        try {
            $sandboxData = $this->engine->testSandbox();
            
            return view('fiscal::sandbox', compact('sandboxData'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
