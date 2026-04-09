<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\CRM\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        return view('crm::customers.index');
    }

    public function datatable(Request $request)
    {
        $query = Customer::select('customers.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query,
                $request,
                ['name', 'email', 'phone', 'document'],
                function ($customer) {
                    $doc = $customer->document ?? 'Não Informado';
                    $clienteHtml = "<div class='font-bold text-slate-800'>{$customer->name}</div><div class='text-xs text-slate-500'>CPF: {$doc}</div>";
                    
                    $email = $customer->email ?? 'N/A';
                    $phone = $customer->phone ?? 'N/A';
                    $contatoHtml = "<div class='text-slate-700'>{$email}</div><div class='text-slate-500'>{$phone}</div>";
                    
                    $pontosHtml = "<div class='font-mono font-bold text-indigo-600'><i class='fa fa-star text-amber-400'></i> " . number_format($customer->points, 0, ',', '.') . " pts</div>";
                    
                    if ($customer->last_purchase_date) {
                        $days = $customer->last_purchase_date->diffInDays(now());
                        $color = $days > 60 ? '#f43f5e' : '#10b981';
                        $dataHtml = "<div class='text-slate-700'>{$customer->last_purchase_date->format('d/m/Y')}</div><div style='font-size:0.75rem; font-weight:bold; color:{$color};'>Há {$days} dias</div>";
                    } else {
                        $dataHtml = "<span class='text-slate-400'>-</span>";
                    }

                    return [
                        'cliente' => $clienteHtml,
                        'contato' => $contatoHtml,
                        'pontos_html' => $pontosHtml,
                        'data_html' => $dataHtml
                    ];
                }
            )
        );
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
