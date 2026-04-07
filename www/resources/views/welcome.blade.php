<x-layouts.app>
    <x-slot:title>Painel Gerencial</x-slot:title>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; color: var(--primary); font-weight: 800; letter-spacing: -0.025em;">Visão Estratégica</h1>
            <p style="color: var(--text-secondary); margin-top: 0.25rem;">Monitoramento D-0 do Ecossistema ERP.</p>
        </div>
        <a href="{{ route('sales.pos.board') }}" style="background: var(--accent); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            Acessar PDV (Caixa)
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
            <x-ui.table>
                <x-slot:header>
                    <tr>
                        <th style="padding: 1rem; text-align: left;">Fluxo</th>
                        <th style="padding: 1rem; text-align: left;">Origem</th>
                        <th style="padding: 1rem; text-align: right;">Montante</th>
                    </tr>
                </x-slot:header>
                
                @forelse($recentTransactions as $trans)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem;">
                            @if($trans->type == 'INCOME')
                                <span style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">ENTRADA</span>
                            @else
                                <span style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">SAÍDA</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            {{ $trans->payment_method ?? 'MISTO' }}<br>
                            <span style="font-size: 0.75rem; color: var(--text-secondary);">Recibo: #{{ $trans->source_id }}</span>
                        </td>
                        <td style="padding: 1rem; text-align: right; font-weight: 600; color: {{ $trans->type == 'INCOME' ? 'var(--success)' : 'var(--danger)' }};">
                            R$ {{ number_format($trans->amount_cents / 100, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-secondary);">Nenhum pulso financeiro registrado.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        <!-- Cupons Recentes -->
        <x-ui.card>
            <x-slot:header>Saídas Recentes no PDV</x-slot:header>
            <x-ui.table>
                <x-slot:header>
                    <tr>
                        <th style="padding: 1rem; text-align: left;">Cupom ID</th>
                        <th style="padding: 1rem; text-align: center;">Itens (Mix)</th>
                        <th style="padding: 1rem; text-align: right;">Total Faturado</th>
                    </tr>
                </x-slot:header>
                
                @forelse($recentSales as $sale)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem; font-weight: 500;">
                            # {{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}<br>
                            <span style="font-size: 0.75rem; color: var(--text-secondary);">{{ $sale->created_at->format('H:i') }}</span>
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            {{ $sale->items->sum('quantity') }} pçs
                        </td>
                        <td style="padding: 1rem; text-align: right; font-weight: 600; color: var(--text-primary);">
                            R$ {{ number_format($sale->total_cents / 100, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-secondary);">Os caixas estão vazios neste momento.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
    </div>
</x-layouts.app>
