<x-layouts.app>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl fw-bold text-primary">Ordens de Compra & Entradas</h2>
            <p class="text-slate-500">Controle de recebimento de cargas (NF-e) e auditoria de reposição.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('purchasing.suppliers.index') }}" class="btn btn-outline">
                Gerenciar Fornecedores
            </a>
            <a href="{{ route('purchasing.orders.create') }}" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700">
                + Nova Entrada / Bipar Carga
            </a>
        </div>
    </div>

    <div class="card bg-transparent border-0 shadow-none">
        <div class="card-body p-0">
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left">Pedido / NF</th>
                        <th class="p-4 text-left">Fornecedor</th>
                        <th class="p-4 text-center">Status Mestre</th>
                        <th class="p-4 text-center">Qtd Itens</th>
                        <th class="p-4 text-right">Valor Total</th>
                        <th class="p-4 text-right">Ação</th>
                    </tr>
                </x-slot>
                
                <x-slot name="body">
                    @forelse($orders as $order)
                    <tr class="border-b transition hover:bg-slate-50">
                        <td class="p-4">
                            <strong class="text-slate-800">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            <div class="text-xs text-slate-500 mt-1">
                                NF: {{ $order->invoice_number ?? 'Sem Nota' }}
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="font-semibold text-slate-700">{{ $order->supplier->company_name ?? 'Desconhecido' }}</span>
                        </td>
                        <td class="p-4 text-center">
                            @if($order->status === 'RECEIVED')
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded">RECEBIDO</span>
                                <div class="text-xs text-emerald-600 mt-1" title="Entrou no estoque">{{ $order->received_at->format('d/m/y H:i') }}</div>
                            @elseif($order->status === 'PENDING')
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded">RASCUNHO / EM TRÂNSITO</span>
                            @else
                                <span class="bg-slate-100 text-slate-800 text-xs font-bold px-2 py-1 rounded">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            {{ $order->items->count() }} Lote(s)
                        </td>
                        <td class="p-4 text-right font-bold text-slate-800">
                            {{ $order->total }}
                        </td>
                        <td class="p-4 text-right">
                            @if($order->status === 'PENDING')
                            <form action="{{ route('purchasing.orders.receive', $order->id) }}" method="POST" id="receive-form-{{$order->id}}">
                                @csrf
                                <button type="button" onclick="confirmReceive({{$order->id}})" class="btn text-sm py-1 px-3 bg-emerald-600 hover:bg-emerald-700 text-white border-none rounded shadow-sm">
                                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Dar Entrada
                                </button>
                            </form>
                            @else
                            <button disabled class="btn text-sm py-1 px-3 bg-slate-200 text-slate-500 border-none rounded" title="Apenas visualização em auditoria futura">
                                Fechado
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">
                            Nenhum pedido de compra ou nota registrada no sistema.
                        </td>
                    </tr>
                    @endforelse
                </x-slot>
            </x-ui.table>
        </div>
    </div>
</x-layouts.app>
