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
        $employees = Employee::orderBy('name')->get();
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($employees);
        }
        return view('hr::employees.index', compact('employees'));
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
            'status' => 'nullable|string',
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
                $status = $emp->status === 'ACTIVE' ? 'Ativo' : 'Desativado';

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
