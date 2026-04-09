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
            <div class="overflow-x-auto p-6">
                <table class="display responsive w-full" id="finance-transactions-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
                            <th class="p-4 font-semibold text-left">ID / Data</th>
                            <th class="p-4 font-semibold text-left">Tipo / Meio</th>
                            <th class="p-4 font-semibold text-left">Origem Polimórfica (Recibo)</th>
                            <th class="p-4 font-semibold text-left">Autoridade</th>
                            <th class="p-4 font-semibold text-right">Montante</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const initFinanceTable = () => {
                        if (typeof window.AppServerTable !== 'function') {
                            setTimeout(initFinanceTable, 100);
                            return;
                        }
                        
                        new window.AppServerTable('#finance-transactions-table', '{{ route('finance.transactions.datatable') }}', [
                            { data: 'date_id', searchable: true },
                            { data: 'tipo', searchable: false },
                            { data: 'origem', searchable: false, orderable: false },
                            { data: 'autor', searchable: false },
                            { data: 'montante', searchable: false, className: 'text-right' }
                        ], [[0, 'desc']]); // As financeiras ordenamos por Data decrescente (ID)
                    };
                    initFinanceTable();
                });
            </script>
        </div>
    </div>
</x-layouts.app>
