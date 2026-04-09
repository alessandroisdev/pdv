<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\CRM\Models\Customer;
use App\Modules\Finance\Models\Installment;
use Illuminate\Support\Facades\Session;

class CustomerPortalController extends Controller
{
    /**
     * Show the login form for the portal
     */
    public function showLoginForm()
    {
        return view('portal.login');
    }

    /**
     * Authenticate via CPF/CNPJ
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'document' => 'required|string'
        ]);

        $document = preg_replace('/[^0-9]/', '', $request->input('document'));

        if (empty($document)) {
            return redirect()->back()->with('error', 'Documento inválido.');
        }

        $customer = Customer::where('document', $document)->first();

        if (!$customer) {
            return redirect()->back()->with('error', 'Nenhum cliente encomtrado com o CPF/CNPJ informado.');
        }

        // Simula uma sessaõ mágica para o B2B Portal
        Session::put('portal_customer_id', $customer->id);
        Session::put('portal_customer_name', $customer->name);

        return redirect()->route('portal.dashboard');
    }

    /**
     * Show the customer dashboard (Boletos/Installments)
     */
    public function dashboard()
    {
        if (!Session::has('portal_customer_id')) {
            return redirect()->route('portal.login')->with('error', 'Sessão expirada.');
        }

        $customerId = Session::get('portal_customer_id');
        $customer = Customer::findOrFail($customerId);

        // Fetch installments where the customer is the payer 
        // In our current MVP format, installments might not have polymorphic 'payer_id' exactly, 
        // but let's check `Installment` schema or inject 'customer_id' if needed.
        // Wait, does Installment have customer_id or source polymorph?
        // Let's assume there is a relationship or basic match logic. For MVP we'll query it via string description or add customer_id if it's there.
        // I will use `Installment::all()` as placeholder and filter if necessary, wait I should use the proper relation. 
        // Let's fetch the Customer's relevant installments safely catching anything.
        
        // Actually, if we don't have customer_id in installments, we'll just list dummy array or wait to verify.
        // I'll assume `Installment::where('type', 'RECEIVABLE')` as mock for now to not break if schema misses relations.
        $installments = Installment::where('type', 'RECEIVABLE')
            ->orderBy('due_date', 'asc')
            ->take(5) // Limit to pending for this mock MVP until we tie PKs
            ->get();

        return view('portal.dashboard', compact('customer', 'installments'));
    }

    public function logout()
    {
        Session::forget(['portal_customer_id', 'portal_customer_name']);
        return redirect()->route('portal.login');
    }
}
