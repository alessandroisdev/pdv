<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\CRM\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('last_purchase_date', 'desc')->paginate(30);
        return view('crm::customers.index', compact('customers'));
    }

    public function broadcast(Request $request)
    {
        // Mocking um envio de Webhook/Email
        $validated = $request->validate([
            'message' => 'required|string|min:10',
            'audience' => 'required|in:ALL,INACTIVE'
        ]);

        $query = Customer::query();
        if ($validated['audience'] === 'INACTIVE') {
            $query->whereDate('last_purchase_date', '<', now()->subDays(60));
        }

        $count = $query->count();
        // Dispararia job do redis aqui: EnviarEmailMarketingJob::dispatch($query->pluck('email'), $validated['message']);

        return redirect()->back()->with('success', "Campanha Webhook enviada para $count clientes da base!");
    }
}
