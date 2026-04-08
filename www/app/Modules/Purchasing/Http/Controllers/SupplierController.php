<?php

namespace App\Modules\Purchasing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Purchasing\Models\Supplier;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Gestão de Fornecedores",
 *     description="Operações de CRUD de parceiros do Supply Chain"
 * )
 */
class SupplierController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/purchasing/suppliers",
     *     tags={"Gestão de Fornecedores"},
     *     summary="Listar Fornecedores",
     *     description="Obtém lista de fornecedores.",
     *     @OA\Response(response=200, description="Lista carregada")
     * )
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::orderBy('company_name')->get();
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($suppliers);
        }
        return view('purchasing::suppliers.index', compact('suppliers'));
    }

    /**
     * @OA\Post(
     *     path="/api/purchasing/suppliers",
     *     tags={"Gestão de Fornecedores"},
     *     summary="Criar Fornecedor",
     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'cnpj_cpf' => 'required|string|unique:suppliers,cnpj_cpf',
            'trade_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $supplier = Supplier::create($data);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($supplier, 201);
        }
        return redirect()->route('purchasing.suppliers.index')->with('success', 'Fornecedor adicionado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'cnpj_cpf' => 'required|string|max:20',
            'trade_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $supplier->update($data);
        return back()->with('success', 'Fornecedor Atualizado!');
    }

    public function destroy($id)
    {
        Supplier::destroy($id);
        return back()->with('success', 'Fornecedor Excluído!');
    }
}
