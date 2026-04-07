<?php

namespace App\Modules\Purchasing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('company_name')->get();
        return view('purchasing::suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'cnpj_cpf' => 'required|string|unique:suppliers,cnpj_cpf',
            'trade_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        Supplier::create($request->all());

        return redirect()->route('purchasing.suppliers.index')->with('success', 'Fornecedor adicionado com sucesso!');
    }
}
