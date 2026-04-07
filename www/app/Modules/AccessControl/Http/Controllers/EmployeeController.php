<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

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

        return redirect()->back()->with('success', 'Funcionário registrado com sucesso.');
    }
}
