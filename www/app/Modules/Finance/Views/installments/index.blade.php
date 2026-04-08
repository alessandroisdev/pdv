<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl fw-bold text-slate-800">Tesouraria (Contas a Pagar / Receber)</h2>
                <p class="text-slate-500">Gestão de Fornecedores, Faturas e Boletos de Devedores.</p>
            </div>
            <div class="flex gap-2" x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold shadow shadow-indigo-200 transition-colors">
                    <i class="fa fa-plus mr-2"></i> Lançar Título
                </button>
                
                <!-- Modal de Novo Título -->
                <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openModal = false"></div>
                        <div class="relative align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form action="{{ route('finance.installments.store') }}" method="POST">
                                @csrf
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-slate-800">Cadastrar Novo Título</h3>
                                    <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                                </div>
                                <div class="p-6 grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tipo de Título</label>
                                        <select name="type" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                            <option value="PAYABLE">Conta a Pagar (Despesa/Fornecedor)</option>
                                            <option value="RECEIVABLE">Conta a Receber (Receita/Fiado)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Descrição</label>
                                        <input type="text" name="description" required placeholder="Ex: Boleto Coca-Cola Mês 04" class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Valor (R$)</label>
                                            <input type="number" step="0.01" min="0.01" name="amount_total" required placeholder="150.00" class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Vencimento</label>
                                            <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                                    <button type="button" @click="openModal = false" class="px-4 py-2 text-slate-600 font-semibold hover:bg-slate-200 rounded-lg transition-colors">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow shadow-indigo-200">Salvar Título</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores Críticos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-rose-50 rounded-xl shadow-sm border border-rose-200 p-6 flex flex-col justify-center items-center text-center">
                <div class="text-rose-600 font-bold mb-1 uppercase text-sm tracking-widest"><i class="fa fa-exclamation-triangle"></i> Contas a Pagar Vencidas</div>
                <div class="text-3xl font-black text-rose-700">{{ $overduePayablesCount }}</div>
                <div class="text-xs text-rose-500 mt-2">Correm juros e multa</div>
            </div>
            <div class="bg-emerald-50 rounded-xl shadow-sm border border-emerald-200 p-6 flex flex-col justify-center items-center text-center">
                <div class="text-emerald-700 font-bold mb-1 uppercase text-sm tracking-widest"><i class="fa fa-clock"></i> Inadimplência a Receber</div>
                <div class="text-3xl font-black text-emerald-800">{{ $overdueReceivablesCount }}</div>
                <div class="text-xs text-emerald-600 mt-2">Fiados / Boletos Vencidos Faltando Cobrar</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Filtros Superiores -->
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex gap-4">
                <a href="{{ route('finance.installments.index') }}" class="px-3 py-1 bg-white border border-slate-200 rounded-full text-sm font-semibold {{ !request('type') ? 'ring-2 ring-indigo-500 text-indigo-700' : 'text-slate-600' }}">Todos</a>
                <a href="{{ route('finance.installments.index', ['type' => 'PAYABLE']) }}" class="px-3 py-1 bg-white border border-slate-200 rounded-full text-sm font-semibold {{ request('type') == 'PAYABLE' ? 'ring-2 ring-rose-500 text-rose-700' : 'text-slate-600' }}">Só A Pagar</a>
                <a href="{{ route('finance.installments.index', ['type' => 'RECEIVABLE']) }}" class="px-3 py-1 bg-white border border-slate-200 rounded-full text-sm font-semibold {{ request('type') == 'RECEIVABLE' ? 'ring-2 ring-emerald-500 text-emerald-700' : 'text-slate-600' }}">Só A Receber</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-sm border-b border-slate-200 bg-white">
                            <th class="p-4 font-semibold">Tipo</th>
                            <th class="p-4 font-semibold">Descrição do Título</th>
                            <th class="p-4 font-semibold">Valor</th>
                            <th class="p-4 font-semibold">Vencimento</th>
                            <th class="p-4 font-semibold">Status / Pagamento</th>
                            <th class="p-4 font-semibold text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($installments as $inst)
                            @php
                                $isOverdue = $inst->status === 'PENDING' && $inst->due_date->isPast();
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors {{ $isOverdue ? 'bg-rose-50/30' : '' }}">
                                <td class="p-4">
                                    @if($inst->type === 'PAYABLE')
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">A PAGAR</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">A RECEBER</span>
                                    @endif
                                </td>
                                <td class="p-4 font-medium text-slate-800">
                                    {{ $inst->description }}
                                </td>
                                <td class="p-4 font-mono font-bold text-slate-700">
                                    {{ new App\Modules\Core\ValueObjects\Money($inst->amount_cents) }}
                                </td>
                                <td class="p-4">
                                    <div class="{{ $isOverdue ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $inst->due_date->format('d/m/Y') }}
                                        @if($isOverdue)
                                            <i class="fa fa-exclamation-circle ml-1"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($inst->status === 'PAID')
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700">PAGO EM {{ $inst->paid_date->format('d/m/y') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-700">PENDENTE</span>
                                    @endif
                                    
                                    @if($inst->transaction_id)
                                        <div class="text-[10px] text-slate-400 mt-1">Ref Livro Razão: #{{ $inst->transaction_id }}</div>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @if($inst->status === 'PENDING')
                                        <form action="{{ route('finance.installments.pay', $inst) }}" method="POST" onsubmit="return confirm('Deseja dar a baixa neste título? O valor entrará/sairá do Livro Razão (Caixa).')">
                                            @csrf
                                            <button type="submit" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg font-bold text-sm transition-colors border border-indigo-200">
                                                <i class="fa fa-check-circle"></i> Dar Baixa
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    <div class="text-4xl mb-4 text-slate-300"><i class="fa fa-calendar-check"></i></div>
                                    <p>Nenhum título lançado na tesouraria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($installments->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $installments->appends(request()->all())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
