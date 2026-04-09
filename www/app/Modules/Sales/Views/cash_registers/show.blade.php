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
            <h3 style="font-weight: bold; color: #455073; text-transform:uppercase; font-size: 0.85rem;">Cupons & Vendas do Turno ({{ $totalSalesCount }})</h3>
            <span style="background: #10b981; color: white; padding: 2px 10px; border-radius: 999px; font-weight:bold; font-size: 0.75rem;">+ R$ {{ number_format($totalSalesCents / 100, 2, ',', '.') }} Movimento</span>
        </div>
        
        <div class="card-body" style="padding: 0; overflow-x: auto;">
            <table class="display responsive nowrap w-100" id="sales-detail-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                        <th style="padding: 1rem; text-align: left;">Cupom Fiscal / ID</th>
                        <th style="padding: 1rem; text-align: left;">Cliente</th>
                        <th style="padding: 1rem; text-align: left;">Operador Responsável</th>
                        <th style="padding: 1rem; text-align: left;">Horário</th>
                        <th style="padding: 1rem; text-align: right;">Total Líquido R$</th>
                    </tr>
                </thead>
            </table>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const initSalesTable = () => {
                    if (typeof window.AppServerTable !== 'function') {
                        setTimeout(initSalesTable, 100);
                        return;
                    }
                    new window.AppServerTable('#sales-detail-table', '/vendas/caixas/{{ $register->id }}/sales/datatable', [
                        { data: 'cupom', name: 'id', searchable: false },
                        { data: 'cliente', name: 'customer_document', searchable: true },
                        { data: 'operador', name: 'seller_id', searchable: false, orderable: false },
                        { data: 'hora', name: 'created_at', searchable: false },
                        { data: 'total', name: 'total_cents', searchable: false, className: 'text-right' }
                    ], [[0, 'desc']]); // Fallback default Cupom DESC
                };
                initSalesTable();
            });
        </script>
    </div>
</x-layouts.app>
