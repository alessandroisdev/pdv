<x-layouts.app>
    <x-slot:title>Painel Gerencial</x-slot:title>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 class="fw-bold" style="font-size: 1.75rem; color: #455073; letter-spacing: -0.025em;">Visão Estratégica</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Monitoramento D-0 do Ecossistema ERP.</p>
        </div>
        <a href="{{ route('sales.pos.board') }}" class="btn btn-contrast" style="padding: 0.75rem 1.5rem; font-size: 1rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            Acessar PDV (Caixa Livre)
        </a>
    </div>

    <!-- Cards de Métricas Principais -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <x-ui.card>
            <x-slot:header>Faturamento (Hoje)</x-slot:header>
            <h2 style="font-size: 2.25rem; color: var(--success); font-weight: 800;">R$ {{ number_format($faturamentoHojeCents / 100, 2, ',', '.') }}</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.85rem;">Volume diário apurado</p>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>Qtd. Vendas (Hoje)</x-slot:header>
            <h2 style="font-size: 2.25rem; color: var(--primary); font-weight: 800;">{{ $totalVendasHoje }}</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.85rem;">Cupons emitidos no PDV</p>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>Ticket Médio D-0</x-slot:header>
            <h2 style="font-size: 2.25rem; color: var(--primary-light); font-weight: 800;">R$ {{ number_format($ticketMedioCents / 100, 2, ',', '.') }}</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.85rem;">Média gasta por cupom</p>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>Acumulado Histórico</x-slot:header>
            <h2 style="font-size: 2.25rem; color: var(--text-primary); font-weight: 800;">R$ {{ number_format($faturamentoTotalAcumuladoCents / 100, 2, ',', '.') }}</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.85rem;">Ativo Realizado Integral</p>
        </x-ui.card>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Livro Razão Recente -->
        <x-ui.card>
            <x-slot:header>Pulsos Financeiros</x-slot:header>
            <table class="display" id="dashboard-transactions-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                        <th style="padding: 1rem; text-align: left;">Fluxo</th>
                        <th style="padding: 1rem; text-align: left;">Origem</th>
                        <th style="padding: 1rem; text-align: right;">Montante</th>
                    </tr>
                </thead>
            </table>
        </x-ui.card>

        <!-- Cupons Recentes -->
        <x-ui.card>
            <x-slot:header>Saídas Recentes no PDV</x-slot:header>
            <table class="display" id="dashboard-sales-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                        <th style="padding: 1rem; text-align: left;">Cupom ID</th>
                        <th style="padding: 1rem; text-align: center;">Itens (Mix)</th>
                        <th style="padding: 1rem; text-align: right;">Total Faturado</th>
                    </tr>
                </thead>
            </table>
        </x-ui.card>
    </div>

    <!-- Init Dashboard DataTables -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initDashboardTables = () => {
                if (typeof window.AppServerTable !== 'function') {
                    setTimeout(initDashboardTables, 100);
                    return;
                }

                new window.AppServerTable('#dashboard-transactions-table', '{{ route('dashboard.transactions.datatable') }}', [
                    { data: 'fluxo', name: 'type', searchable: false },
                    { data: 'origem', name: 'payment_method', searchable: false },
                    { data: 'montante', name: 'amount_cents', searchable: false, className: 'text-right' }
                ], [[0, 'desc']], { pageLength: 5, lengthChange: false, searching: false, info: false });

                new window.AppServerTable('#dashboard-sales-table', '{{ route('dashboard.sales.datatable') }}', [
                    { data: 'cupom', name: 'id', searchable: false },
                    { data: 'itens', searchable: false, orderable: false, className: 'text-center' },
                    { data: 'faturado', name: 'total_cents', searchable: false, className: 'text-right' }
                ], [[0, 'desc']], { pageLength: 5, lengthChange: false, searching: false, info: false });
            };
            initDashboardTables();
        });
    </script>
</x-layouts.app>
