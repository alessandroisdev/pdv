<x-layouts.app>
    <x-slot:title>Auditoria | Caixa #{{ str_pad($register->id, 5, '0', STR_PAD_LEFT) }}</x-slot:title>

    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl fw-bold text-primary">Auditoria de Caixa #{{ str_pad($register->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <p class="text-light">Detalhamento completo do turno e vendas registradas.</p>
        </div>
        <a href="{{ route('sales.cash_registers.index') }}" class="btn btn-outline" style="border: none; padding: 0.5rem;">
            <i class="fa fa-arrow-left"></i> Voltar para Caixas
        </a>
    </div>

    <div class="grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="border-left: 4px solid #3b82f6;">
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.75rem; color: #64748b; font-weight: bold; text-transform: uppercase;">Operador de Frente</p>
                <h3 style="font-size: 1.25rem; font-weight: bold; color: #1e293b; margin-top: 0.5rem;">{{ $register->operator->name ?? 'PDV User' }}</h3>
                <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">
                    Status: <span style="font-weight:bold; color: {{ $register->closed_at ? '#ef4444' : '#10b981' }}">{{ $register->closed_at ? 'FECHADO' : 'ABERTO EM OPERAÇÃO' }}</span>
                </p>
            </div>
        </div>

        <div class="card" style="border-left: 4px solid #10b981;">
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.75rem; color: #64748b; font-weight: bold; text-transform: uppercase;">Abertura / Fundo Inicial</p>
                <h3 style="font-size: 1.25rem; font-weight: bold; color: #059669; margin-top: 0.5rem;">R$ {{ number_format($register->initial_cents / 100, 2, ',', '.') }}</h3>
                <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem;">Data: {{ $register->opened_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        @php
            $totalSalesCents = $register->sales->sum('total_cents');
            $expectedTotal = $register->initial_cents + $totalSalesCents;
            $hasDivergence = $register->closed_at && $register->difference_cents != 0;
        @endphp

        <div class="card" style="border-left: 4px solid {{ $hasDivergence ? '#ef4444' : '#64748b' }};">
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.75rem; color: #64748b; font-weight: bold; text-transform: uppercase;">Fechamento / Caixa Lançado</p>
                <h3 style="font-size: 1.25rem; font-weight: bold; color: {{ $hasDivergence ? '#ef4444' : '#1e293b' }}; margin-top: 0.5rem;">
                    @if($register->closed_at)
                    R$ {{ number_format(($register->reported_cents ?? 0) / 100, 2, ',', '.') }}
                    @else
                    ---
                    @endif
                </h3>
                <p style="font-size: 0.75rem; color: {{ $hasDivergence ? '#ef4444' : '#94a3b8' }}; margin-top: 0.5rem;">
                    @if($register->closed_at)
                        Quebra/Dif: R$ {{ number_format(($register->difference_cents ?? 0) / 100, 2, ',', '.') }} | Em: {{ $register->closed_at->format('d/m/Y H:i') }}
                    @else
                        Esperado C/ Vendas: R$ {{ number_format($expectedTotal / 100, 2, ',', '.') }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Lista de Vendas -->
    <div class="card">
        <div class="card-header border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 style="font-weight: bold; color: #455073; text-transform:uppercase; font-size: 0.85rem;">Cupons & Vendas do Turno ({{ $register->sales->count() }})</h3>
            <span style="background: #10b981; color: white; padding: 2px 10px; border-radius: 999px; font-weight:bold; font-size: 0.75rem;">+ R$ {{ number_format($totalSalesCents / 100, 2, ',', '.') }} Movimento</span>
        </div>
        
        <div class="card-body" style="padding: 0; overflow-x: auto;">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                        <th style="padding: 1rem; text-align: left;">Cupom Fiscal / ID</th>
                        <th style="padding: 1rem; text-align: left;">Cliente</th>
                        <th style="padding: 1rem; text-align: left;">Operador Responsável</th>
                        <th style="padding: 1rem; text-align: left;">Horário</th>
                        <th style="padding: 1rem; text-align: right;">Total Líquido R$</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($register->sales as $sale)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                        <td style="padding: 1rem; font-weight: bold; color: #1e293b;">
                            #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td style="padding: 1rem; color: #455073;">
                            {{ $sale->customer->name ?? 'CONSUMIDOR (SEM NOME)' }}
                            <div style="font-size: 0.75rem; color: #94a3b8; font-family: monospace; margin-top:2px;">
                                {{ $sale->customer_document ?? 'Sem CPF' }}
                            </div>
                        </td>
                        <td style="padding: 1rem; color: #64748b;">{{ $sale->seller->name ?? 'Usuário Desconhecido' }}</td>
                        <td style="padding: 1rem; color: #64748b;">{{ $sale->created_at ? $sale->created_at->format('H:i:s') : '--' }}</td>
                        <td style="padding: 1rem; text-align: right; font-weight: 900; color: #059669; font-size: 1.1rem; letter-spacing: -0.5px;">
                            {{ number_format($sale->total_cents / 100, 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 4rem 2rem; text-align: center;">
                            <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"><i class="fa fa-receipt"></i></div>
                            <h4 style="font-size: 1.125rem; font-weight: bold; color: #455073; margin-bottom: 0.25rem;">Turno Sem Movimentação de Vendas!</h4>
                            <p style="color: #64748b; font-size: 0.875rem;">Ainda não foram processadas frentes de caixa para este turno operacional.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
