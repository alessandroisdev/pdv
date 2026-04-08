<x-layouts.app>
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl fw-bold text-primary">Ordens de Compra & Entradas</h2>
            <p class="text-light">Controle de recebimento de cargas (NF-e) e auditoria de reposição.</p>
        </div>
        <div class="flex" style="gap: 10px;">
            <a href="{{ route('purchasing.suppliers.index') }}" class="btn btn-outline">
                Gerenciar Fornecedores
            </a>
            <a href="{{ route('purchasing.orders.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> &nbsp; Nova Entrada / Bipar Carga
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0; overflow-x: auto;">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                        <th style="padding: 1rem; text-align: left;">Pedido / NF</th>
                        <th style="padding: 1rem; text-align: left;">Fornecedor</th>
                        <th style="padding: 1rem; text-align: center;">Status Mestre</th>
                        <th style="padding: 1rem; text-align: center;">Qtd Itens</th>
                        <th style="padding: 1rem; text-align: right;">Valor Total</th>
                        <th style="padding: 1rem; text-align: right;">Ação</th>
                    </tr>
                </thead>
                
                <tbody>
                    @forelse($orders as $order)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                        <td style="padding: 1rem;">
                            <strong style="color: #1e293b;">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            <div class="text-light mt-1" style="font-size: 0.75rem;">
                                NF: {{ $order->invoice_number ?? 'Sem Nota' }}
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            <span style="font-weight: 600; color: #455073;">{{ $order->supplier->company_name ?? 'Desconhecido' }}</span>
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            @if($order->status === 'RECEIVED')
                                <span style="background-color: #d1fae5; color: #047857; font-size: 0.75rem; font-weight: bold; padding: 2px 6px; border-radius: 4px;">RECEBIDO</span>
                                <div style="font-size: 0.75rem; color: #059669; margin-top: 4px;" title="Entrou no estoque">{{ $order->received_at->format('d/m/y H:i') }}</div>
                            @elseif($order->status === 'PENDING')
                                <span style="background-color: #fef3c7; color: #92400e; font-size: 0.75rem; font-weight: bold; padding: 2px 6px; border-radius: 4px;">RASCUNHO / EM TRÂNSITO</span>
                            @else
                                <span style="background-color: #f1f5f9; color: #1e293b; font-size: 0.75rem; font-weight: bold; padding: 2px 6px; border-radius: 4px;">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center; color: #64748b;">
                            {{ $order->items->count() }} Lote(s)
                        </td>
                        <td style="padding: 1rem; text-align: right; font-weight: bold; color: #1e293b;">
                            {{ $order->total }}
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            @if($order->status === 'PENDING')
                            <form action="{{ route('purchasing.orders.receive', $order->id) }}" method="POST" id="receive-form-{{$order->id}}">
                                @csrf
                                <button type="button" onclick="confirmReceive({{$order->id}})" class="btn" style="background-color: #059669; color: white; padding: 0.25rem 0.75rem; font-size: 0.875rem; border: none;">
                                    <i class="fa fa-arrow-down" style="font-size: 10px;"></i> Entrada
                                </button>
                            </form>
                            @else
                            <button disabled class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.875rem; opacity: 0.5;" title="Apenas visualização em auditoria futura">
                                Fechado
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: #64748b;">
                            Nenhum pedido de compra ou nota registrada no sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
