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
        return view('settings::users.index');
    }

    public function datatable(Request $request)
    {
        $query = User::select('users.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['name', 'email'],
                function ($u) {
                    $nomeBadge = "<div style='font-weight:bold; color:#1e293b;'>{$u->name}</div>
                                  <div style='font-size:0.75rem; color:#94a3b8;'>Desde " . $u->created_at->format('d/m/Y') . "</div>";

                    $emailHtml = $u->email;

                    if ($u->role === 'MANAGER') {
                        $roleHtml = "<span style='display:inline-flex; align-items:center; padding:0.25rem 0.5rem; border-radius:9999px; font-size:0.7rem; font-weight:bold; background:#e0e7ff; color:#4338ca;'>
                                        <i class='fa fa-shield-alt mr-1'></i> GESTOR
                                     </span>";
                    } else {
                        $roleHtml = "<span style='display:inline-flex; align-items:center; padding:0.25rem 0.5rem; border-radius:9999px; font-size:0.7rem; font-weight:bold; background:#f1f5f9; color:#475569;'>
                                        CAIXISTA
                                     </span>";
                    }

                    $btnEdit = "<button type='button' onclick=\"document.getElementById('modal-edit-user-{$u->id}').showModal()\" style='background:white; border:1px solid #4338ca; color:#4338ca; padding:0.35rem 0.75rem; border-radius:0.35rem; font-size:0.75rem; font-weight:bold; cursor:pointer; transition:all 0.2s;' title='Editar'>Editar</button>";

                    if ($u->id !== \Illuminate\Support\Facades\Auth::id()) {
                        $btnDelRoute = route('settings.users.destroy', $u->id);
                        $csrfToken = csrf_token();
                        $btnDel = "<form action='{$btnDelRoute}' method='POST' style='margin:0;' onsubmit=\"return confirm('Deseja excluir permanentemente este usuário? Removê-lo impedirá que o mesmo acesse o PDV.')\">
                                        <input type='hidden' name='_token' value='{$csrfToken}'>
                                        <input type='hidden' name='_method' value='DELETE'>
                                        <button type='submit' style='background:white; border:1px solid #ef4444; color:#ef4444; padding:0.35rem 0.75rem; border-radius:0.35rem; font-size:0.75rem; font-weight:bold; cursor:pointer; transition:all 0.2s;' title='Bloquear / Remover'>Remover</button>
                                   </form>";
                    } else {
                        $btnDel = "<span style='font-size:0.75rem; color:#10b981; font-weight:bold; border:1px solid #10b981; background:#ecfdf5; padding:0.35rem 0.75rem; border-radius:0.35rem; margin-left:0.5rem;'>Sessão Atual</span>";
                    }

                    return [
                        'm_name' => $nomeBadge,
                        'email' => $emailHtml,
                        'role' => $roleHtml,
                        'acoes' => "<div style='display:flex; justify-content:flex-end; gap:0.5rem; align-items:center;'>" . $btnEdit . $btnDel . "</div>",
                        '_raw_user' => $u // Usado apenas como auxiliar no frontend caso queiram renderizar modais invisiveis? 
                    ];
                }
            )
        );
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
