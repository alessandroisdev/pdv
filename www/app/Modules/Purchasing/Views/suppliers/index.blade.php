<x-layouts.app>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl fw-bold text-primary">Gestão de Fornecedores</h2>
        <button class="btn btn-primary" onclick="window.ui.openModal('modal-add-supplier')">
            + Novo Parceiro Mestre
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left">Razão Social (Empresa)</th>
                        <th class="p-4 text-left">CNPJ / CPF</th>
                        <th class="p-4 text-left">Contato</th>
                        <th class="p-4 text-left">Status</th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @forelse($suppliers as $supplier)
                    <tr class="border-b transition hover:bg-slate-50">
                        <td class="p-4">
                            <strong class="text-slate-800">{{ $supplier->company_name }}</strong><br>
                            <small class="text-slate-500">{{ $supplier->trade_name ?? '---' }}</small>
                        </td>
                        <td class="p-4">{{ $supplier->cnpj_cpf }}</td>
                        <td class="p-4">
                            <div>{{ $supplier->email ?? 'Sem E-mail' }}</div>
                            <div class="text-slate-500 text-sm">{{ $supplier->phone ?? 'Sem Telefone' }}</div>
                        </td>
                        <td class="p-4">
                            @if($supplier->is_active)
                                <span class="badge text-xs bg-emerald-100 text-emerald-700 font-bold" style="padding: 2px 6px; border-radius:4px;">ATIVO</span>
                            @else
                                <span class="badge text-xs bg-slate-100 text-slate-700 font-bold" style="padding: 2px 6px; border-radius:4px;">INATIVO</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-500">Nenhum fornecedor logístico cadastrado.</td>
                    </tr>
                    @endforelse
                </x-slot>
            </x-ui.table>
        </div>
    </div>

    <!-- Modal Novo Fornecedor -->
    <dialog id="modal-add-supplier" class="modal rounded-lg shadow-xl" style="width: 600px; padding: 2rem; border: none; outline: none;">
        <h3 class="text-xl fw-bold text-primary mb-4">Credenciar Indústria / Distribuidor</h3>
        <form action="{{ route('purchasing.suppliers.store') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label>Razão Social (Obrigatório)</label>
                <input type="text" name="company_name" class="form-control w-full" required>
            </div>
            
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label>Nome Fantasia</label>
                    <input type="text" name="trade_name" class="form-control w-full">
                </div>
                <div class="form-group">
                    <label>CPNJ ou CPF (Obrigatório)</label>
                    <input type="text" name="cnpj_cpf" class="form-control w-full" required>
                </div>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>E-mail P/ Notas Fiscais</label>
                    <input type="email" name="email" class="form-control w-full">
                </div>
                <div class="form-group">
                    <label>Telefone / WhatsApp</label>
                    <input type="text" name="phone" class="form-control w-full">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-slate-200">
                <button type="button" class="btn btn-outline" onclick="window.ui.closeModal('modal-add-supplier')">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="background:#10b981; border:none;">Registrar Fornecedor</button>
            </div>
        </form>
    </dialog>
</x-layouts.app>
