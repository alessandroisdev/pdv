<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        // Permite atrelar operadores a contas web reais
        $webUsers = User::all();
        return view('accesscontrol::employees.index', compact('employees', 'webUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'required|string|min:4|unique:employees,pin',
            'level' => 'required|in:OPERATOR,SUPERVISOR'
        ]);

        Employee::create([
            'name' => $request->input('name'),
            'pin' => $request->input('pin'),
            'level' => $request->input('level'),
            'user_id' => $request->input('user_id'),
            'status' => true
        ]);

        return redirect()->back()->with('success', 'Colaborador registrado com sucesso!');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pin' => 'required|string|min:4|unique:employees,pin,' . $employee->id,
            'level' => 'required|in:OPERATOR,SUPERVISOR'
        ]);

        $employee->update([
            'name' => $request->input('name'),
            'pin' => $request->input('pin'),
            'level' => $request->input('level'),
            'user_id' => $request->input('user_id'),
        ]);

        return redirect()->back()->with('success', 'Colaborador atualizado com sucesso!');
    }

    public function toggleStatus(Employee $employee)
    {
        $employee->update(['status' => !$employee->status]);
        return redirect()->back()->with('success', 'Status do colaborador alterado.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->back()->with('success', 'Colaborador removido da frente de caixa.');
    }

    public function badge(Employee $employee)
    {
        // 1. Array tático
        $payload = json_encode([
            'id' => $employee->id,
            'pin' => $employee->pin,
            'level' => $employee->level,
            'sys' => 'pdv_modular'
        ]);
        
        // 2. AES-256-CBC via facade do Laravel
        $qrHash = Crypt::encryptString($payload);
        
        // 3. Código de barras linear usando o mesmo provedor Picqer já consolidado nas etiquetas
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $barcodeSVG = $generator->getBarcode($employee->pin, $generator::TYPE_CODE_128);

        return view('accesscontrol::employees.badge', compact('employee', 'qrHash', 'barcodeSVG'));
    }
}
