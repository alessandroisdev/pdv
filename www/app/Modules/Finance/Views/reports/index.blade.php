<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl fw-bold text-slate-800">Relatórios Automáticos & Analytics</h2>
                <p class="text-slate-500">Métricas analíticas baseadas no Livro Razão.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('finance.dashboard') }}" class="btn bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-semibold shadow-sm transition-colors">
                    <i class="fa fa-arrow-left mr-2"></i> Voltar ao Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center items-center text-center">
                <div class="text-slate-500 font-semibold mb-2 uppercase text-sm tracking-widest">Faturamento Diário</div>
                <div class="text-3xl font-bold text-slate-800">R$ {{ number_format($todayIncome / 100, 2, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center items-center text-center border-l-4 border-rose-500">
                <div class="text-slate-500 font-semibold mb-2 uppercase text-sm tracking-widest">Despesas Diárias</div>
                <div class="text-3xl font-bold text-rose-600">R$ {{ number_format($todayExpense / 100, 2, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center items-center text-center border-l-4 border-emerald-500">
                <div class="text-slate-500 font-semibold mb-2 uppercase text-sm tracking-widest">Margem Contribuição</div>
                <div class="text-3xl font-bold text-emerald-600">{{ number_format($profitMargin, 1, ',', '') }}%</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-2">Evolução do Faturamento (Últimos 7 Dias)</h3>
            <canvas id="financeChart" height="100"></canvas>
        </div>
    </div>

    <!-- Chart.js Injection -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('financeChart').getContext('2d');
            const chartData = @json($chartData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Entradas (R$)',
                            data: chartData.income,
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 2,
                            borderRadius: 6
                        },
                        {
                            label: 'Saídas (R$)',
                            data: chartData.expense,
                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.app>
