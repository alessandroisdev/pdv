<x-layouts.app>
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Relatórios Automáticos & Analytics</h2>
            <p class="text-light" style="margin-top: 0.25rem;">Métricas analíticas baseadas no Livro Razão.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('finance.dashboard') }}" class="btn btn-outline" style="background: white;">
                <i class="fa fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                <div style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;">Faturamento Diário</div>
                <div style="font-size: 1.875rem; font-weight: bold; color: #1e293b;">R$ {{ number_format($todayIncome / 100, 2, ',', '.') }}</div>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid #f43f5e;">
            <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                <div style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;">Despesas Diárias</div>
                <div style="font-size: 1.875rem; font-weight: bold; color: #e11d48;">R$ {{ number_format($todayExpense / 100, 2, ',', '.') }}</div>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid #10b981;">
            <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                <div style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;">Margem Contribuição</div>
                <div style="font-size: 1.875rem; font-weight: bold; color: #059669;">{{ number_format($profitMargin, 1, ',', '') }}%</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">Evolução do Faturamento (Últimos 7 Dias)</h3>
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
