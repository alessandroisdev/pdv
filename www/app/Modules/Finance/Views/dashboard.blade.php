<x-layouts.app>
    <div class="mb-4 flex flex-wrap justify-between items-center" style="margin-bottom: 1.5rem;">
        <div>
            <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Painel Financeiro & Auditoria</h2>
            <p class="text-light" style="margin-top: 0.25rem;">Visão Analítica de Receitas, Despesas e Monitoramento de Risco</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance.installments.index') }}" class="btn btn-outline" style="background-color: white; border-color:#e2e8f0; color:#334155;">
                <i class="fa fa-file-invoice-dollar"></i> Pagar / Receber
            </a>
            <a href="{{ route('finance.reports.index') }}" class="btn btn-outline" style="background-color: #f8fafc; border-color:#cbd5e1; color:#0f172a;">
                <i class="fa fa-chart-pie"></i> DRE & Relatórios
            </a>
            <a href="{{ route('finance.transactions.export') }}" class="btn btn-primary" style="background: #10b981; border-color: #10b981; color:white; font-weight:bold;">
                <i class="fa fa-file-excel"></i> CSV Export
            </a>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="card" style="border-left: 4px solid #6366f1;">
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Saldo em Caixa Real</p>
                <h3 style="font-size: 1.875rem; font-weight: bold; color: #1e293b; margin-top: 0.5rem; margin-bottom: 0;">R$ {{ number_format($metrics['cash_balance'], 2, ',', '.') }}</h3>
                <p style="font-size: 0.75rem; font-weight: bold; color: #4f46e5; margin-top: 0.5rem; margin-bottom: 0;"><i class="fa fa-arrow-up"></i> +12.5% este mês</p>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid #10b981;">
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Receitas Brutas</p>
                <h3 style="font-size: 1.875rem; font-weight: bold; color: #1e293b; margin-top: 0.5rem; margin-bottom: 0;">R$ {{ number_format($metrics['monthly_incomes'], 2, ',', '.') }}</h3>
                <p style="font-size: 0.75rem; font-weight: bold; color: #059669; margin-top: 0.5rem; margin-bottom: 0;"><i class="fa fa-check-circle"></i> Meta Batida</p>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid #f43f5e;">
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Despesas / Variáveis</p>
                <h3 style="font-size: 1.875rem; font-weight: bold; color: #1e293b; margin-top: 0.5rem; margin-bottom: 0;">R$ {{ number_format($metrics['monthly_expenses'], 2, ',', '.') }}</h3>
                <p style="font-size: 0.75rem; font-weight: bold; color: #e11d48; margin-top: 0.5rem; margin-bottom: 0;"><i class="fa fa-exclamation-triangle"></i> Atenção aos Gastos</p>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid #f59e0b;">
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Custos Fixos e Folha</p>
                <h3 style="font-size: 1.875rem; font-weight: bold; color: #1e293b; margin-top: 0.5rem; margin-bottom: 0;">R$ {{ number_format($metrics['fixed_costs'], 2, ',', '.') }}</h3>
                <p style="font-size: 0.75rem; font-weight: bold; color: #d97706; margin-top: 0.5rem; margin-bottom: 0;"><i class="fa fa-lock"></i> Contas Mensais</p>
            </div>
        </div>
    </div>

    <!-- Ocular Central: Gráficos e Auditoria -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        
        <!-- Grafico Fluxo -->
        <div>
            <div class="card" style="height: 100%;">
                <div class="card-header" style="background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-weight: bold; color: #334155; margin: 0; text-transform: uppercase; font-size: 0.875rem;"><i class="fa fa-chart-bar" style="color: #6366f1;"></i> DRE e Fluxo de Caixa Diário</h3>
                </div>
                <div class="card-body">
                    <canvas id="cashflowChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Alertas de Auditoria -->
        <div>
            <div class="card" style="height: 100%; border-color: #ffe4e6;">
                <div class="card-header" style="background: #fff1f2; border-bottom: 1px solid #ffe4e6; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-weight: bold; color: #be123c; margin: 0; text-transform: uppercase; font-size: 0.875rem;"><i class="fa fa-shield-alt"></i> Alertas de Auditoria</h3>
                    <span style="background: #e11d48; color: white; font-size: 0.75rem; font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 999px;">{{ $criticalAudits->count() }} Novos</span>
                </div>
                <div class="card-body" style="padding: 0;">
                    @forelse($criticalAudits as $audit)
                        <div style="padding: 1rem; border-bottom: 1px solid #f8fafc; transition: background 0.2s;">
                            <div style="display: flex; gap: 0.75rem;">
                                <div style="background: #ffe4e6; color: #e11d48; border-radius: 999px; height: 2.5rem; width: 2.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fa fa-trash"></i>
                                </div>
                                <div>
                                    <h4 style="font-size: 0.875rem; font-weight: bold; color: #1e293b; margin: 0;">Exclusão: {{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</h4>
                                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; margin-bottom: 0;">Por Usuário ID: {{ $audit->user_id ?? 'Sistema' }}</p>
                                    <p style="font-size: 0.75rem; font-weight: 600; color: #e11d48; margin-top: 0.25rem; margin-bottom: 0;">{{ rtrim($audit->created_at->diffForHumans(), ' atrás') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 2rem; text-align: center; color: #94a3b8;">
                            <i class="fa fa-check-circle" style="font-size: 2.25rem; margin-bottom: 0.75rem; color: #34d399;"></i>
                            <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">Tudo tranquilo.</p>
                            <p style="font-size: 0.75rem; margin: 0;">Nenhuma exclusão crítica registrada recentemente.</p>
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
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: 'Receitas (Entradas)',
                            data: {!! json_encode($chartData['income']) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 4
                        },
                        {
                            label: 'Despesas (Saídas)',
                            data: {!! json_encode($chartData['expense']) !!},
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
