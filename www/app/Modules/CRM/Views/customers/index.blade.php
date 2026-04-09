<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 flex flex-wrap justify-between items-center border-b border-slate-200 pb-4" style="margin-bottom: 1.5rem; padding-bottom: 1rem;">
            <div>
                <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Marketing & CRM (Clube de Vantagens)</h2>
                <p class="text-light" style="margin-top: 0.25rem;">Gestão de Consumidores, Fidelidade e Automação de Retenção.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('crm-broadcast-modal').showModal()" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5;">
                    <i class="fa fa-bullhorn" style="margin-right: 0.5rem;"></i> Disparo em Massa
                </button>
            </div>
        </div>

        <!-- Modal de Broadcast -->
        <dialog id="crm-broadcast-modal" style="padding: 0; border: none; border-radius: 0.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); width: 100%; max-width: 32rem; max-height: 90vh; position: fixed; inset: 0; margin: auto; z-index: 9999;">
            <style>
                #crm-broadcast-modal::backdrop {
                    background: rgba(15, 23, 42, 0.5);
                    backdrop-filter: blur(2px);
                }
            </style>
            <form action="{{ route('crm.customers.broadcast') }}" method="POST">
                @csrf
                <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10;">
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">Disparo de E-mail/WhatsApp</h3>
                    <button type="button" onclick="document.getElementById('crm-broadcast-modal').close()" style="background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.5rem; font-weight: bold; line-height: 1;">&times;</button>
                </div>
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; text-transform: uppercase; font-size: 0.75rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Público-Alvo (Audiência)</label>
                        <select name="audience" required class="form-control" style="width: 100%;">
                            <option value="ALL" {{ old('audience') == 'ALL' ? 'selected' : '' }}>Todo Mundo (Promoção Geral)</option>
                            <option value="INACTIVE" {{ old('audience') == 'INACTIVE' ? 'selected' : '' }}>Inativos (Não compram há 60 dias)</option>
                        </select>
                        @error('audience')
                            <div style="color: #e11d48; font-size: 0.75rem; margin-top: 0.25rem; font-weight: bold;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="margin-top: 1rem;">
                        <label style="display: block; text-transform: uppercase; font-size: 0.75rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Mensagem da Campanha</label>
                        <textarea name="message" required rows="4" placeholder="Ex: Estamos com saudades! Use o cupom VOLTA20..." class="form-control" style="width: 100%;">{{ old('message') }}</textarea>
                        @error('message')
                            <div style="color: #e11d48; font-size: 0.75rem; margin-top: 0.25rem; font-weight: bold;">Atenção: {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    <button type="button" onclick="document.getElementById('crm-broadcast-modal').close()" class="btn btn-outline" style="background: white; border-color: #cbd5e1; color: #475569;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5; font-weight: bold;"><i class="fa fa-paper-plane" style="margin-right: 0.5rem;"></i> Lançar Campanha</button>
                </div>
            </form>
        </dialog>

        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('crm-broadcast-modal').showModal();
                });
            </script>
        @endif

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Sucesso!',
                            text: '{{ session('success') }}',
                            icon: 'success',
                            confirmButtonColor: '#4f46e5'
                        });
                    } else {
                        alert('{{ session('success') }}');
                    }
                });
            </script>
        @endif

        <div class="card bg-white border-0 shadow-sm p-0 overflow-hidden" style="border-radius: 0.75rem;">
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Cliente</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Contato</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Pontos (Nível)</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Última Compra</th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @forelse($customers as $customer)
                        <tr class="border-b transition hover:bg-slate-50" style="border-bottom: 1px solid #f1f5f9;">
                            <td class="p-4" style="padding: 1rem;">
                                <div class="font-bold text-slate-800" style="color: #1e293b; font-weight: bold;">{{ $customer->name }}</div>
                                <div class="text-xs text-slate-500" style="font-size: 0.75rem; color: #64748b;">CPF: {{ $customer->document ?? 'Não Informado' }}</div>
                            </td>
                            <td class="p-4 text-sm" style="padding: 1rem; font-size: 0.875rem;">
                                <div class="text-slate-700" style="color: #334155;">{{ $customer->email ?? 'N/A' }}</div>
                                <div class="text-slate-500" style="color: #64748b;">{{ $customer->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="p-4 font-mono font-bold text-indigo-600" style="padding: 1rem; font-family: monospace; font-weight: bold; color: #4f46e5;">
                                <i class="fa fa-star" style="color: #fbbf24; margin-right: 0.25rem;"></i> {{ number_format($customer->points, 0, ',', '.') }} pts
                            </td>
                            <td class="p-4" style="padding: 1rem; font-size: 0.875rem;">
                                @if($customer->last_purchase_date)
                                      @php
                                         $days = $customer->last_purchase_date->diffInDays(now());
                                      @endphp
                                      <div class="text-slate-700" style="color: #334155;">{{ $customer->last_purchase_date->format('d/m/Y') }}</div>
                                      <div style="font-size: 0.75rem; font-weight: bold; color: {{ $days > 60 ? '#f43f5e' : '#10b981' }};">Há {{ $days }} dias</div>
                                @else
                                      <span class="text-slate-400" style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500" style="padding: 2rem; text-align: center; color: #64748b;">
                                <div class="text-4xl mb-4 text-slate-300" style="font-size: 2.25rem; margin-bottom: 1rem; color: #cbd5e1;"><i class="fa fa-users"></i></div>
                                <p>Sua base de CRM está vazia ou os caixistas ainda não coletaram CPFs suficientes.</p>
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-ui.table>
            
            @if($customers->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50" style="padding: 1rem; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
