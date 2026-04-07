<?php

namespace App\Modules\Purchasing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Purchasing\Models\Supplier;
use App\Modules\Purchasing\Http\Requests\SupplierStoreRequest;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(15);
        return view('purchasing::suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('purchasing::suppliers.create');
    }

    public function store(SupplierStoreRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()->route('purchasing.suppliers.index')
            ->with('success', 'Fornecedor cadastrado com sucesso!');
    }
}
