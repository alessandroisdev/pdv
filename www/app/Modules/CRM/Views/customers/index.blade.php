<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl fw-bold text-slate-800">Marketing & CRM (Clube de Vantagens)</h2>
                <p class="text-slate-500">Gestão de Consumidores, Fidelidade e Automação de Retenção.</p>
            </div>
            <div class="flex gap-2" x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold shadow shadow-indigo-200 transition-colors">
                    <i class="fa fa-bullhorn mr-2"></i> Disparo em Massa
                </button>
                
                <!-- Modal de Broadcast -->
                <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openModal = false"></div>
                        <div class="relative align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form action="{{ route('crm.customers.broadcast') }}" method="POST">
                                @csrf
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-slate-800">Disparo de E-mail/WhatsApp</h3>
                                    <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                                </div>
                                <div class="p-6 grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Público-Alvo (Audiência)</label>
                                        <select name="audience" required class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded">
                                            <option value="ALL">Todo Mundo (Promoção Geral)</option>
                                            <option value="INACTIVE">Inativos (Não compram há 60 dias)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Mensagem da Campanha</label>
                                        <textarea name="message" required rows="4" placeholder="Ex: Estamos com saudades! Use o cupom VOLTA20 nas compras acima de R$100." class="form-control w-full bg-slate-50 border border-slate-200 p-2 rounded"></textarea>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                                    <button type="button" @click="openModal = false" class="px-4 py-2 text-slate-600 font-semibold hover:bg-slate-200 rounded-lg transition-colors">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow shadow-indigo-200"><i class="fa fa-paper-plane mr-2"></i> Lançar Campanha</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-sm border-b border-slate-200 bg-white">
                            <th class="p-4 font-semibold">Cliente</th>
                            <th class="p-4 font-semibold">Contato</th>
                            <th class="p-4 font-semibold">Pontos (Nível)</th>
                            <th class="p-4 font-semibold">Última Compra</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-slate-800">{{ $customer->name }}</div>
                                    <div class="text-xs text-slate-400">CPF: {{ $customer->document ?? 'Não Informado' }}</div>
                                </td>
                                <td class="p-4 text-sm">
                                    <div class="text-slate-700">{{ $customer->email ?? 'N/A' }}</div>
                                    <div class="text-slate-500">{{ $customer->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="p-4 font-mono font-bold text-indigo-600">
                                    <i class="fa fa-star text-amber-400 mr-1"></i> {{ number_format($customer->points, 0, ',', '.') }} pts
                                </td>
                                <td class="p-4">
                                    @if($customer->last_purchase_date)
                                      @php
                                         $days = $customer->last_purchase_date->diffInDays(now());
                                      @endphp
                                      <div class="text-slate-700">{{ $customer->last_purchase_date->format('d/m/Y') }}</div>
                                      <div class="text-xs {{ $days > 60 ? 'text-rose-500 font-bold' : 'text-emerald-500' }}">Há {{ $days }} dias</div>
                                    @else
                                      <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500">
                                    <div class="text-4xl mb-4 text-slate-300"><i class="fa fa-users"></i></div>
                                    <p>Sua base de CRM está vazia ou os caixistas ainda não coletaram CPFs suficientes.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($customers->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
