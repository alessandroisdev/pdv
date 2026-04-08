<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Livro Razão Transacional</h2>
                <p class="text-slate-500">Auditoria contábil imutável (Append-Only) e visibilidade total do caixa.</p>
            </div>
            <a href="{{ route('finance.transactions.export') }}" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold shadow shadow-indigo-200 transition-colors">
                <i class="fa fa-download mr-2"></i> Exportar Auditoria (CSV)
            </a>
        </div>

        <!-- Indicadores Financeiros Totais -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-emerald-50 rounded-xl shadow-sm border border-emerald-200 p-6 flex flex-col justify-center items-center text-center">
                <div class="text-emerald-700 font-bold mb-1 uppercase text-sm tracking-widest"><i class="fa fa-arrow-up"></i> Total de Entradas (Ativos)</div>
                <div class="text-3xl font-black text-emerald-800">+ {{ format_money($income) }}</div>
            </div>
            <div class="bg-rose-50 rounded-xl shadow-sm border border-rose-200 p-6 flex flex-col justify-center items-center text-center">
                <div class="text-rose-600 font-bold mb-1 uppercase text-sm tracking-widest"><i class="fa fa-arrow-down"></i> Total de Saídas (Passivos)</div>
                <div class="text-3xl font-black text-rose-700">- {{ format_money($expense) }}</div>
            </div>
            <div class="bg-indigo-50 rounded-xl shadow-sm border border-indigo-200 p-6 flex flex-col justify-center items-center text-center">
                <div class="text-indigo-700 font-bold mb-1 uppercase text-sm tracking-widest"><i class="fa fa-wallet"></i> Saldo Operacional Líquido</div>
                <div class="text-3xl font-black {{ $balanceCents >= 0 ? 'text-indigo-800' : 'text-rose-700' }}">
                    {{ format_money($balanceCents) }}
                </div>
            </div>
        </div>

        <!-- Indicadores Táticos do Dia -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mb-2">Faturamento Hoje</p>
                    <h3 class="text-3xl text-slate-800 font-black m-0">{{ format_money($todayIncome) }}</h3>
                </div>
                <div class="bg-blue-50 text-blue-500 p-4 rounded-full text-2xl shadow-inner">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mb-2">Ticket Médio (Hoje)</p>
                    <h3 class="text-3xl text-slate-800 font-black m-0">{{ format_money($ticketMedio) }}</h3>
                </div>
                <div class="bg-fuchsia-50 text-fuchsia-500 p-4 rounded-full text-2xl shadow-inner">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>
        </div>

        <!-- Alertas de Auditoria -->
        @if(isset($caixasComDivergencia) && count($caixasComDivergencia) > 0)
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-6 mb-8 shadow-sm">
            <h3 class="text-rose-600 mt-0 mb-4 text-lg font-bold flex items-center gap-2"><i class="fa fa-exclamation-triangle"></i> ALERTAS DE AUDITORIA: QUEBRA DE CAIXA</h3>
            <div class="grid gap-4">
                @foreach($caixasComDivergencia as $caixa)
                    <div class="bg-white p-4 rounded-lg border-l-4 border-rose-500 flex justify-between items-center shadow-sm">
                        <div>
                            <strong class="text-slate-700">Turno de Caixa #{{ str_pad($caixa->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            <div class="text-sm text-slate-500 mt-1">Fechado em: {{ $caixa->closed_at ? clone $caixa->closed_at->format('d/m/Y H:i') : 'Desconhecido' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-rose-500 uppercase tracking-widest font-bold">Divergência Reportada</div>
                            <strong class="text-rose-600 text-xl">R$ {{ number_format($caixa->difference_cents / 100, 2, ',', '.') }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Tabela Geral de Transações -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
                            <th class="p-4 font-semibold w-1/10">ID / Data</th>
                            <th class="p-4 font-semibold w-1/6">Tipo / Meio</th>
                            <th class="p-4 font-semibold w-2/5">Origem Polimórfica (Recibo)</th>
                            <th class="p-4 font-semibold w-1/5">Autoridade</th>
                            <th class="p-4 font-semibold text-right w-1/6">Montante</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 text-slate-500 tabular-nums">
                                    <strong class="text-indigo-600">#{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</strong><br>
                                    <span class="text-xs">{{ $tx->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                
                                <td class="p-4">
                                    @if($tx->type == 'INCOME')
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700">ENTRADA</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-rose-100 text-rose-700">SAÍDA</span>
                                    @endif
                                    <div class="text-xs font-bold text-slate-400 mt-1 uppercase">
                                        VIA {{ $tx->payment_method ?? 'ND' }}
                                    </div>
                                </td>
                                
                                <td class="p-4">
                                    @if($tx->source_type === \App\Modules\Sales\Models\Sale::class)
                                        <div class="flex items-center gap-2">
                                            <div class="bg-slate-100 border border-slate-200 px-3 py-1 rounded-md text-sm text-slate-700"><i class="fa fa-shopping-cart text-slate-400 mr-2"></i> Transação Fechada em Balcão (PDV)</div>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">Vínculo Interno: Cupom de Venda #{{ $tx->source_id }}</div>
                                    @else
                                        <span class="text-slate-500 italic text-sm">Lançamento Manual Avulso</span>
                                    @endif
                                </td>
                                
                                <td class="p-4 text-sm">
                                    <strong class="text-slate-800">{{ optional($tx->actor)->name ?? 'Sistema' }}</strong><br>
                                    <span class="text-xs text-slate-500">Caixa Físico</span>
                                </td>
                                
                                <td class="p-4 text-right text-lg font-bold tabular-nums {{ $tx->type == 'INCOME' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->type == 'INCOME' ? '+' : '-' }} {{ clone $tx->amount }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">
                                    <div class="text-4xl mb-4 text-slate-300 opacity-50"><i class="fa fa-shield"></i></div>
                                    <strong class="text-slate-700 block text-lg mb-1">Nenhuma Transação Financeira!</strong>
                                    <p class="text-sm">O Livro Razão está intacto e vazio. As vendas de Caixa ou Entradas Manuais ecoarão aqui.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
