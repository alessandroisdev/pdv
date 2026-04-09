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
        if ($request->wantsJson() || $request->is('api/*')) {
            $suppliers = Supplier::orderBy('company_name')->get();
            return response()->json($suppliers);
        }
        return view('purchasing::suppliers.index');
    }

    public function datatable(Request $request)
    {
        $query = Supplier::select('suppliers.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['company_name', 'trade_name', 'cnpj_cpf', 'email', 'phone'],
                function ($supplier) {
                    $razaoHtml = "  <strong style='color:#1e293b;'>{$supplier->company_name}</strong><br>
                                    <small class='text-light'>" . ($supplier->trade_name ?? '---') . "</small>";
                    
                    $cnpjCpf = $supplier->cnpj_cpf;

                    $contatoHtml = "<div>" . ($supplier->email ?? 'Sem E-mail') . "</div>
                                    <div class='text-light text-sm'>" . ($supplier->phone ?? 'Sem Telefone') . "</div>";

                    $statusBadge = $supplier->is_active
                        ? "<span style='background-color:#d1fae5; color:#047857; font-size:0.75rem; font-weight:bold; padding:2px 6px; border-radius:4px;'>ATIVO</span>"
                        : "<span style='background-color:#f1f5f9; color:#475569; font-size:0.75rem; font-weight:bold; padding:2px 6px; border-radius:4px;'>INATIVO</span>";

                    return [
                        'razao' => $razaoHtml,
                        'documento' => $cnpjCpf,
                        'contato' => $contatoHtml,
                        'status' => $statusBadge,
                    ];
                }
            )
        );
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
