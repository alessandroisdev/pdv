<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl fw-bold text-slate-800">Controle de Usuários e Permissões</h2>
                <p class="text-slate-500">Gestão de caixistas, gerentes e administradores do ERP.</p>
            </div>
            <div class="flex gap-2" x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold shadow shadow-indigo-200 transition-colors">
                    <i class="fa fa-user-plus mr-2"></i> Novo Usuário
                </button>
                
                <!-- Modal Novo User -->
                <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openModal = false"></div>
                        <div class="relative align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form action="{{ route('settings.users.store') }}" method="POST">
                                @csrf
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-slate-800">Adicionar Usuário ao Sistema</h3>
                                    <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                                </div>
                                <div class="p-6 grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nome Completo</label>
                                        <input type="text" name="name" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">E-mail de Login</label>
                                        <input type="email" name="email" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Senha Padrão Inicial</label>
                                        <input type="password" name="password" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nível de Acesso (Papel)</label>
                                        <select name="role" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                            <option value="CASHIER">Caixista / Operador de Loja</option>
                                            <option value="MANAGER">Gestor Administrativo (Configurações)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                                    <button type="button" @click="openModal = false" class="px-4 py-2 text-slate-600 font-semibold hover:bg-slate-200 rounded-lg transition-colors">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow shadow-indigo-200">Criar Usuário</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                            <th class="p-4 font-semibold">Identificação</th>
                            <th class="p-4 font-semibold">E-mail</th>
                            <th class="p-4 font-semibold">Hierarquia</th>
                            <th class="p-4 font-semibold text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $u)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-slate-800">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-400">Desde {{ $u->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="p-4 text-slate-600">{{ $u->email }}</td>
                                <td class="p-4">
                                    @if($u->role === 'MANAGER')
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-indigo-100 text-indigo-700"><i class="fa fa-shield-alt mr-1"></i> GESTOR</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-slate-100 text-slate-700">CAIXISTA</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('settings.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente este usuário? Removê-lo impedirá que o mesmo acesse o PDV.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded font-bold text-sm transition-colors border border-transparent hover:border-rose-200">
                                                <i class="fa fa-trash"></i> Bloquear/Remover
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-emerald-500 font-bold border border-emerald-200 px-2 py-1 rounded bg-emerald-50">Você (Sessão Atual)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
