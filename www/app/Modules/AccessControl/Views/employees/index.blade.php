<x-layouts.app>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl fw-bold text-primary">Gestão de Colaboradores Físicos (PIN)</h2>
        <button class="btn btn-primary" onclick="document.getElementById('modal-add-employee').showModal()">
            + Novo Colaborador
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left">PIN Físico</th>
                        <th class="p-4 text-left">Nome</th>
                        <th class="p-4 text-left">Nível Operacional</th>
                        <th class="p-4 text-left">Conta Web</th>
                        <th class="p-4 text-left">Status</th>
                        <th class="p-4 text-right">Ações</th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @foreach($employees as $employee)
                    <tr class="border-b transition hover:bg-slate-50">
                        <td class="p-4 font-bold text-slate-800">
                            {{ $employee->pin }}
                        </td>
                        <td class="p-4">{{ $employee->name }}</td>
                        <td class="p-4">
                            @if($employee->level === 'SUPERVISOR')
                                <span class="badge text-xs bg-red-100 text-red-700" style="padding: 2px 6px; border-radius:4px; font-weight:bold;">SUPERVISOR</span>
                            @else
                                <span class="badge text-xs bg-slate-100 text-slate-700" style="padding: 2px 6px; border-radius:4px; font-weight:bold;">OPERADOR</span>
                            @endif
                        </td>
                        <td class="p-4">
                            {{ $employee->user ? $employee->user->email : 'Nenhuma' }}
                        </td>
                        <td class="p-4">
                            @if($employee->status)
                                <span class="text-emerald-600 font-bold">Ativo</span>
                            @else
                                <span class="text-slate-500">Inativo</span>
                            @endif
                        </td>
                        <td class="p-4 text-right flex justify-end gap-2">
                            <!-- Badge Maker Button -->
                            <a href="{{ route('employees.badge', $employee->id) }}" class="btn btn-outline p-2 text-indigo-600 border-indigo-200 hover:bg-indigo-50" title="Imprimir Crachá" target="_blank">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            </a>

                            <!-- Toggle Status Form -->
                            <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-outline p-2 {{ $employee->status ? 'text-amber-600 border-amber-200' : 'text-emerald-600 border-emerald-200' }}" title="{{ $employee->status ? 'Inativar' : 'Ativar' }}">
                                    @if($employee->status)
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    @else
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </button>
                            </form>

                            <!-- Delete Form -->
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente o operador do sistema?');" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline p-2 text-red-600 border-red-200 hover:bg-red-50" title="Excluir">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </x-slot>
            </x-ui.table>
        </div>
    </div>

    <dialog id="modal-add-employee" class="modal rounded-lg shadow-xl" style="width: 500px; padding: 2rem; border: none; outline: none;">
        <h3 class="text-xl fw-bold text-primary mb-4">Cadastrar Novo Colaborador (PDV Físico)</h3>
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label>Nome Completo</label>
                <input type="text" name="name" class="form-control w-full" required>
            </div>
            
            <div class="form-group mb-4">
                <label>PIN Numérico (Ex: 1234)</label>
                <input type="password" name="pin" class="form-control w-full" maxlength="8" inputmode="numeric" required>
                <small class="text-slate-500">Usado para abrir a gaveta do PDV e logar na máquina local.</small>
            </div>

            <div class="form-group mb-4">
                <label>Papel</label>
                <select name="level" class="form-control w-full">
                    <option value="OPERATOR">Caixa Operador (Nível Base)</option>
                    <option value="SUPERVISOR">Supervisor / Gerente (Autoriza Sangrias e Overrides)</option>
                </select>
            </div>

            <div class="form-group mb-6">
                <label>Vínculo com Módulo Administrativo Web (Opcional)</label>
                <select name="user_id" class="form-control w-full">
                    <option value="">-- Apenas Acesso ao Frente de Caixa --</option>
                    @foreach($webUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-add-employee').close()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Colaborador</button>
            </div>
        </form>
    </dialog>
</x-layouts.app>
