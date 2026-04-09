<?php

namespace App\Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Models\Employee;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Recursos Humanos",
 *     description="Gerenciamento da Equipe e Colaboradores (CRUD & Exportação)"
 * )
 */
class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/hr/employees",
     *     tags={"Recursos Humanos"},
     *     summary="Listar Funcionários",
     *     description="Retorna a lista de colaboradores incluindo dados de folha e status de acesso.",
     *     @OA\Response(
     *         response=200,
     *         description="Operação bem-sucedida"
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            $employees = Employee::orderBy('name')->get();
            return response()->json($employees);
        }
        return view('hr::employees.index');
    }

    public function datatable(Request $request)
    {
        $query = Employee::select('employees.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['name', 'cpf', 'role_description'],
                function ($emp) {
                    $idPad = str_pad($emp->id, 3, '0', STR_PAD_LEFT);
                    $pinCode = $emp->pin ?? 'N/A';
                    $codigoHtml = "<span class='text-slate-800 font-bold'>#{$idPad}</span><br><span class='text-slate-500 font-mono text-xs'>PIN: {$pinCode}</span>";

                    $doc = $emp->cpf ?: 'Não Informado';
                    $nomeHtml = "<div class='font-bold text-slate-800'>{$emp->name}</div><div class='text-slate-500 text-xs mt-1'>CPF: {$doc}</div>";

                    $cargoDesc = $emp->role_description ?? 'Operador Padrão';
                    $admissao = $emp->admission_date ? $emp->admission_date->format('d/m/Y') : '--';
                    $cargoHtml = "{$cargoDesc}<br><span class='text-xs text-slate-400'>Admissão: {$admissao}</span>";

                    $salarioHtml = "R$ " . number_format($emp->base_salary_cents / 100, 2, ',', '.');

                    $statusHtml = $emp->status == 1 
                        ? "<span style='display:inline-block; padding:0.25rem 0.5rem; border-radius:0.25rem; font-size:0.75rem; font-weight:bold; background:#d1fae5; color:#065f46;'>Ativo</span>"
                        : "<span style='display:inline-block; padding:0.25rem 0.5rem; border-radius:0.25rem; font-size:0.75rem; font-weight:bold; background:#ffe4e6; color:#9f1239;'>Desligado</span>";

                    $btnEdit = "<a href='".route('hr.employees.edit', $emp->id)."' class='btn btn-outline' style='padding:0.25rem 0.75rem; font-size:0.875rem;'>Editar</a>";
                    $btnDel = "<form action='".route('hr.employees.destroy', $emp->id)."' method='POST' id='remove-emp-{$emp->id}' style='display:inline-block; margin-left:0.5rem;'><input type='hidden' name='_token' value='".csrf_token()."'><input type='hidden' name='_method' value='DELETE'><button type='button' onclick='confirmRemoval({$emp->id})' class='btn' style='background:#fff1f2; border:1px solid #fecdd3; color:#e11d48; padding:0.25rem 0.75rem; font-size:0.875rem; font-weight:bold;'>Desligar</button></form>";

                    return [
                        'codigo' => $codigoHtml,
                        'nome' => $nomeHtml,
                        'cargo' => $cargoHtml,
                        'salario' => $salarioHtml,
                        'status_html' => $statusHtml,
                        'acoes' => $btnEdit . $btnDel
                    ];
                }
            )
        );
    }

    /**
     * @OA\Post(
     *     path="/api/hr/employees",
     *     tags={"Recursos Humanos"},
     *     summary="Registrar Funcionário",
     *     description="Cadastra um novo colaborador no sistema",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Maria Souza"),
     *                 @OA\Property(property="cpf", type="string", example="12345678909"),
     *                 @OA\Property(property="role_description", type="string", example="Caixa Master"),
     *                 @OA\Property(property="base_salary_cents", type="integer", example=350000),
     *                 @OA\Property(property="pin", type="string", example="4455")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Criado")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'nullable|string|max:10',
            'level' => 'nullable|string',
            'cpf' => 'nullable|string|max:20',
            'rg' => 'nullable|string|max:20',
            'admission_date' => 'nullable|date',
            'base_salary_cents' => 'nullable|integer',
            'role_description' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'bank_account_info' => 'nullable|string',
            'work_schedule' => 'nullable|string',
            
            // Advanced Info
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'rg_issuer' => 'nullable|string|max:50',
            'pis_pasep' => 'nullable|string|max:50',
            'ctps_number' => 'nullable|string|max:50',
            'cep' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:10',
            'neighborhood' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
            'pix_key' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $employee = Employee::create($data);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($employee, 201);
        }
        return redirect()->route('hr.employees.index')->with('success', 'Funcionário Registrado com Sucesso!');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('hr::employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'nullable|string|max:10',
            'level' => 'nullable|string',
            'status' => 'nullable|integer',
            'cpf' => 'nullable|string|max:20',
            'rg' => 'nullable|string|max:20',
            'admission_date' => 'nullable|date',
            'base_salary_cents' => 'nullable|integer',
            'role_description' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'bank_account_info' => 'nullable|string',
            'work_schedule' => 'nullable|string',

            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'rg_issuer' => 'nullable|string|max:50',
            'pis_pasep' => 'nullable|string|max:50',
            'ctps_number' => 'nullable|string|max:50',
            'cep' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:10',
            'neighborhood' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
            'pix_key' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $employee->update($data);

        return redirect()->route('hr.employees.index')->with('success', 'Dados de RH Atualizados!');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return redirect()->route('hr.employees.index')->with('success', 'Funcionário Demitido/Desativado!');
    }

    /**
     * @OA\Get(
     *     path="/api/hr/employees/export",
     *     tags={"Recursos Humanos"},
     *     summary="Exportar Dados RH",
     *     description="Baixa o arquivo CSV com toda a estrutura salarial",
     *     @OA\Response(response=200, description="Arquivo CSV")
     * )
     */
    public function exportCsv(Request $request)
    {
        $fileName = 'colaboradores_export_' . date('Ymd_His') . '.csv';
        $employees = Employee::orderBy('name')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = [
            'ID', 'Nome', 'Status', 'Gênero', 'Estado Civil', 'Data Nasc.', 
            'CPF', 'RG', 'Emissor_RG', 'PIS/PASEP', 'CTPS', 
            'Departamento', 'Função', 'Regime_Contrato', 'Data Admissao', 
            'Salario Base (R$)', 'Telefone', 'CEP', 'Cidade', 'Estado', 'Bairro', 
            'Banco/Ag/Cc', 'Chave PIX', 'Contato Ermegência'
        ];

        $callback = function() use($employees, $columns) {
            $file = fopen('php://output', 'w');
            
            // Corrige caracteres UTF-8 no Excel (BOM)
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, $columns, ';');

            foreach ($employees as $emp) {
                $salaryCalculated = number_format($emp->base_salary_cents / 100, 2, ',', '.');
                $status = $emp->status == 1 ? 'Ativo' : 'Desativado';

                $row = [
                    $emp->id,
                    $emp->name,
                    $status,
                    $emp->gender ?? '-',
                    $emp->marital_status ?? '-',
                    $emp->birth_date ? $emp->birth_date->format('d/m/Y') : '-',
                    $emp->cpf ?? '-',
                    $emp->rg ?? '-',
                    $emp->rg_issuer ?? '-',
                    $emp->pis_pasep ?? '-',
                    $emp->ctps_number ?? '-',
                    $emp->department ?? '-',
                    $emp->role_description ?? '-',
                    $emp->contract_type ?? '-',
                    $emp->admission_date ? $emp->admission_date->format('d/m/Y') : '-',
                    $salaryCalculated,
                    $emp->contact_phone ?? '-',
                    $emp->cep ?? '-',
                    $emp->city ?? '-',
                    $emp->state ?? '-',
                    $emp->neighborhood ?? '-',
                    $emp->bank_account_info ?? '-',
                    $emp->pix_key ?? '-',
                    ($emp->emergency_contact_name ?? '') . ' / ' . ($emp->emergency_contact_phone ?? '')
                ];
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
