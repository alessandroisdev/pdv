<x-layouts.app>
    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl fw-bold text-slate-800">Histórico de Documentos Fiscais</h2>
                <p class="text-slate-500">Monitoramento Sefaz: NFC-e e Notas emitidas no Caixa.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('fiscal.sandbox') }}" class="btn bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-semibold shadow-sm transition-colors">
                    <i class="fa fa-vial mr-2 text-indigo-500"></i> Laboratório / Ping
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                            <th class="p-4 font-semibold">ID / Venda</th>
                            <th class="p-4 font-semibold">Modelo</th>
                            <th class="p-4 font-semibold">Emissão</th>
                            <th class="p-4 font-semibold">Valor</th>
                            <th class="p-4 font-semibold">Status / Recibo</th>
                            <th class="p-4 font-semibold text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-slate-700">#{{ $doc->id }}</div>
                                    <div class="text-xs text-slate-400">Venda: {{ $doc->sale_id }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $doc->document_type ?? 'NFC-E' }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-slate-600">
                                    {{ $doc->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4 font-mono font-medium text-slate-700">
                                    @if($doc->sale)
                                        {{ new App\Modules\Core\ValueObjects\Money($doc->sale->total_cents) }}
                                    @else
                                        ---
                                    @endif
                                </td>
                                <td class="p-4">
                                    @php
                                        $badgeClass = 'bg-slate-100 text-slate-700';
                                        if($doc->status === 'AUTORIZADO') $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                        if($doc->status === 'CONTINGENCIA_OFFLINE') $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                                        if($doc->status === 'CANCELADO') $badgeClass = 'bg-rose-100 text-rose-800 border-rose-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                        {{ $doc->status }}
                                    </span>
                                    @if($doc->protocol_number)
                                        <div class="text-[10px] text-slate-400 mt-1 font-mono">Prot: {{ $doc->protocol_number }}</div>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($doc->status !== 'CANCELADO')
                                        <button type="button" onclick="confirmCancel({{ $doc->id }})" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Cancelar Nota">
                                            <i class="fa fa-ban"></i>
                                        </button>
                                        @endif
                                    </div>
                                    
                                    <form id="cancel-form-{{ $doc->id }}" action="{{ route('fiscal.records.cancel', $doc->id) }}" method="POST" class="hidden">
                                        @csrf
                                        <input type="hidden" name="reason" id="cancel-reason-{{ $doc->id }}" value="Erro operacional local">
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    <div class="text-4xl mb-4">🧾</div>
                                    <p>Nenhum documento fiscal registrado ainda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($documents->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- SweetAlert para Motivo de Cancelamento -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmCancel(docId) {
            Swal.fire({
                title: 'Cancelar NFC-e?',
                text: "A nota já pode ter sido impressa. Deseja estornar perante a Sefaz?",
                icon: 'warning',
                input: 'text',
                inputLabel: 'Motivo do Cancelamento (Mínimo 15 caracteres)',
                inputPlaceholder: 'Ex: Cliente desistiu da compra logo apos emissao',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sim, Cancelar NF',
                cancelButtonText: 'Voltar',
                preConfirm: (reason) => {
                    if (!reason || reason.length < 15) {
                        Swal.showValidationMessage('O motivo deve ter no mínimo 15 caracteres para a Sefaz aceitar.');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancel-reason-' + docId).value = result.value;
                    document.getElementById('cancel-form-' + docId).submit();
                }
            });
        }
    </script>
</x-layouts.app>
