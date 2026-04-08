<x-layouts.app>
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Painel Financeiro & Auditoria</h2>
            <p class="text-slate-500">Visão Analítica de Receitas, Despesas e Monitoramento de Risco</p>
        </div>
        <div class="flex gap-2">
            <a href="/api/documentation" target="_blank" class="btn btn-outline-primary shadow-sm bg-white font-bold border-indigo-200 text-indigo-700">
                <i class="fa fa-book"></i> API Swagger Docs
            </a>
            <a href="{{ route('finance.transactions.export') }}" class="btn btn-primary shadow-md font-bold text-white bg-emerald-600 hover:bg-emerald-700">
                <i class="fa fa-file-excel"></i> Exportar Relatório
            </a>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card bg-white shadow-sm border-l-4 border-indigo-500">
            <div class="card-body p-5">
                <p class="text-sm font-semibold text-slate-500 uppercase">Saldo em Caixa Real</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-2">R$ {{ number_format($metrics['cash_balance'], 2, ',', '.') }}</h3>
                <p class="text-xs text-indigo-600 font-bold mt-2"><i class="fa fa-arrow-up"></i> +12.5% este mês</p>
            </div>
        </div>
        <div class="card bg-white shadow-sm border-l-4 border-emerald-500">
            <div class="card-body p-5">
                <p class="text-sm font-semibold text-slate-500 uppercase">Receitas Brutas</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-2">R$ {{ number_format($metrics['monthly_incomes'], 2, ',', '.') }}</h3>
                <p class="text-xs text-emerald-600 font-bold mt-2"><i class="fa fa-check-circle"></i> Meta Batida</p>
            </div>
        </div>
        <div class="card bg-white shadow-sm border-l-4 border-rose-500">
            <div class="card-body p-5">
                <p class="text-sm font-semibold text-slate-500 uppercase">Despesas / Variáveis</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-2">R$ {{ number_format($metrics['monthly_expenses'], 2, ',', '.') }}</h3>
                <p class="text-xs text-rose-600 font-bold mt-2"><i class="fa fa-exclamation-triangle"></i> Atenção aos Gastos</p>
            </div>
        </div>
        <div class="card bg-white shadow-sm border-l-4 border-amber-500">
            <div class="card-body p-5">
                <p class="text-sm font-semibold text-slate-500 uppercase">Custos Fixos e Folha</p>
                <h3 class="text-3xl font-bold text-slate-800 mt-2">R$ {{ number_format($metrics['fixed_costs'], 2, ',', '.') }}</h3>
                <p class="text-xs text-amber-600 font-bold mt-2"><i class="fa fa-lock"></i> Contas Mensais</p>
            </div>
        </div>
    </div>

    <!-- Ocular Central: Gráficos e Auditoria -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Grafico Fluxo -->
        <div class="lg:col-span-2">
            <div class="card shadow-sm h-full">
                <div class="card-header bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-700 m-0 uppercase text-sm"><i class="fa fa-chart-bar text-indigo-500"></i> DRE e Fluxo de Caixa Diário</h3>
                </div>
                <div class="card-body p-6">
                    <canvas id="cashflowChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Alertas de Auditoria -->
        <div class="lg:col-span-1">
            <div class="card shadow-sm border border-rose-100 h-full">
                <div class="card-header bg-rose-50 border-b border-rose-100 flex justify-between items-center">
                    <h3 class="font-bold text-rose-700 m-0 uppercase text-sm"><i class="fa fa-shield-alt"></i> Alertas de Auditoria</h3>
                    <span class="bg-rose-600 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $criticalAudits->count() }} Novos</span>
                </div>
                <div class="card-body p-0">
                    @forelse($criticalAudits as $audit)
                        <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                            <div class="flex gap-3">
                                <div class="bg-rose-100 text-rose-600 p-2 rounded-full h-10 w-10 flex items-center justify-center shrink-0">
                                    <i class="fa fa-trash"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Exclusão: {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">Por Usuário ID: {{ $audit->user_id ?? 'Sistema' }}</p>
                                    <p class="text-xs font-semibold text-rose-600 mt-1">{{ rtrim($audit->created_at->diffForHumans(), ' atrás') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <i class="fa fa-check-circle text-4xl mb-3 text-emerald-400"></i>
                            <p class="text-sm font-semibold">Tudo tranquilo.</p>
                            <p class="text-xs">Nenhuma exclusão crítica registrada recentemente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Injection -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cashflowChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'],
                    datasets: [
                        {
                            label: 'Receitas (Entradas)',
                            data: [12000, 19000, 15000, 22000, 28000, 35000, 11000],
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 4
                        },
                        {
                            label: 'Despesas (Saídas)',
                            data: [5000, 4000, 8000, 6000, 15000, 12000, 2000],
                            backgroundColor: 'rgba(244, 63, 94, 0.8)',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</x-layouts.app>
