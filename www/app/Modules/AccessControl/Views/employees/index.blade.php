<x-layouts.app>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl fw-bold text-primary">Gestão de Colaboradores Físicos (PIN)</h2>
        <button class="btn btn-primary" onclick="window.ui.openModal('modal-add-employee')">
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
                        <th class="p-4 text-left">Conta Web/Painel Vinculada</th>
                        <th class="p-4 text-left">Status</th>
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
                            @if($employee->user)
                                {{ $employee->user->email }}
                            @else
                                <span class="text-slate-400 italic">Nenhuma (Apenas Físico)</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($employee->status)
                                <span class="text-emerald-600 font-bold">Ativo</span>
                            @else
                                <span class="text-slate-500">Inativo</span>
                            @endif
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
                <button type="button" class="btn btn-outline" onclick="window.ui.closeModal('modal-add-employee')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Colaborador</button>
            </div>
        </form>
    </dialog>
</x-layouts.app>
