<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SystemUserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return view('settings::users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:MANAGER,CASHIER',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Custom role logic we might have or use string directly. We can save in "is_admin" flag.
            // But let's assume we use role if it doesnt exist we can just store manager logic.
            // Let's assume standard Laravel only has name/email/password. 
            // We will add 'role' to database if not exists, but for now we map MANAGER to email checking or we add migration.
            'role' => $request->role 
        ]);

        return redirect()->back()->with('success', 'Usuário de Sistema Criado!');
    }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:MANAGER,CASHIER',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Usuário atualizado!');
    }

    public function destroy(User $user)
    {
        if ($user->id === \Illuminate\Support\Facades\Auth::id()) {
            return redirect()->back()->with('error', 'Você não pode se excluir.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'Usuário removido.');
    }
}
