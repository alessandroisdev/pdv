<x-layouts.app>
    <x-slot:title>Financeiro - Livro Razão</x-slot:title>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; color: var(--primary); font-weight: 800; letter-spacing: -0.025em;">Livro Razão Transacional</h1>
            <p style="color: var(--text-secondary); margin-top: 0.25rem;">Auditoria contábil imutável (Append-Only) e visibilidade total do caixa.</p>
        </div>
        <button class="btn btn-primary">
            Exportar Auditoria (CSV)
        </button>
    </div>

    <!-- Indicadores Financeiros Totais -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <x-ui.card>
            <x-slot:header>Total de Entradas (Ativos)</x-slot:header>
            <h2 style="font-size: 2rem; color: var(--success); font-weight: 800;">+ R$ {{ number_format($income / 100, 2, ',', '.') }}</h2>
        </x-ui.card>
        
        <x-ui.card>
            <x-slot:header>Total de Saídas (Passivos)</x-slot:header>
            <h2 style="font-size: 2rem; color: var(--danger); font-weight: 800;">- R$ {{ number_format($expense / 100, 2, ',', '.') }}</h2>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>Saldo Operacional Líquido</x-slot:header>
            <h2 style="font-size: 2rem; color: {{ $balanceCents >= 0 ? 'var(--primary)' : 'var(--danger)' }}; font-weight: 800;">
                R$ {{ number_format($balanceCents / 100, 2, ',', '.') }}
            </h2>
        </x-ui.card>
    </div>

    <!-- Tabela Geral de Transações -->
    <x-ui.card>
        <x-ui.table>
            <x-slot:head>
                <th style="padding: 1rem; width: 10%;">ID / Data</th>
                <th style="padding: 1rem; width: 15%;">Tipo / Meio</th>
                <th style="padding: 1rem; width: 40%;">Origem Polimórfica (Recibo)</th>
                <th style="padding: 1rem; width: 20%;">Autoridade</th>
                <th style="padding: 1rem; width: 15%; text-align: right;">Montante</th>
            </x-slot:head>
            
            @forelse($transactions as $tx)
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 1rem; color: var(--text-secondary); font-variant-numeric: tabular-nums;">
                        <strong style="color: var(--primary);">#{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</strong><br>
                        {{ $tx->created_at->format('d/m/Y H:i') }}
                    </td>
                    
                    <td style="padding: 1rem;">
                        @if($tx->type == 'INCOME')
                            <span style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">ENTRADA</span>
                        @else
                            <span style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">SAÍDA</span>
                        @endif
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.4rem;">
                            VIA {{ $tx->payment_method ?? 'ND' }}
                        </div>
                    </td>
                    
                    <td style="padding: 1rem;">
                        @if($tx->source_type === \App\Modules\Sales\Models\Sale::class)
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="background: var(--bg-light); border: 1px solid var(--border); padding: 0.4rem; border-radius: 6px;">🛒 Transação Fechada em Balcão (PDV)</div>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">Vincúlo Interno: Cupom de Venda #{{ $tx->source_id }}</div>
                        @else
                            <span style="color: var(--text-secondary); font-style: italic;">Lançamento Manual Avulso</span>
                        @endif
                    </td>
                    
                    <td style="padding: 1rem;">
                        <strong>{{ optional($tx->actor)->name ?? 'Sistema' }}</strong><br>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Caixa Físico</span>
                    </td>
                    
                    <td style="padding: 1rem; text-align: right; font-size: 1.15rem; font-weight: 700; color: {{ $tx->type == 'INCOME' ? 'var(--success)' : 'var(--danger)' }};" class="font-variant-numeric">
                        {{ $tx->type == 'INCOME' ? '+' : '-' }} R$ {{ number_format($tx->amount_cents / 100, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="margin: 0 auto; margin-bottom: 1rem; opacity: 0.5;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"></path></svg>
                        <strong>Nenhuma Transação Financeira!</strong><br>
                        O Livro Razão está intacto e vazio. As vendas de Caixa ou Entradas Manuais ecoarão aqui.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div style="padding: 1rem; border-top: 1px solid var(--border);">
            {{ $transactions->links('pagination::bootstrap-5') }} <!-- Using standard bootstrapped links if any -->
        </div>
    </x-ui.card>
</x-layouts.app>
