<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 border-b border-slate-200 pb-4 flex justify-between items-end" style="margin-bottom: 2rem;">
            <div>
                <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Histórico de Documentos Fiscais</h2>
                <p class="text-light" style="margin-top: 0.25rem;">Monitoramento Sefaz: NFC-e e Notas emitidas no Caixa.</p>
            </div>
            <div>
                <a href="{{ route('fiscal.sandbox') }}" class="btn shadow" style="background: white; border: 1px solid #cbd5e1; color: #475569; padding: 0.75rem 1.25rem; font-weight: bold; border-radius: 0.5rem; text-decoration: none; display: inline-flex; items-center;">
                    <i class="fa fa-vial" style="color: #6366f1; margin-right: 0.5rem;"></i> Laboratório / Ping
                </a>
            </div>
        </div>

        <div class="card bg-white border-0 shadow-sm p-0 overflow-hidden" style="border-radius: 0.75rem;">
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">ID / Venda</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Modelo</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Emissão</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Valor</th>
                        <th class="p-4 text-left font-semibold" style="padding: 1rem;">Status / Recibo</th>
                        <th class="p-4 text-right font-semibold" style="padding: 1rem;">Ações</th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @forelse($documents as $doc)
                        <tr class="border-b transition hover:bg-slate-50" style="border-bottom: 1px solid #f1f5f9;">
                            <td class="p-4" style="padding: 1rem;">
                                <div style="font-weight: bold; color: #334155;">#{{ $doc->id }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">Venda: {{ $doc->sale_id }}</div>
                            </td>
                            <td class="p-4" style="padding: 1rem;">
                                <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.7rem; font-weight: bold; background: #f1f5f9; color: #1e293b;">
                                    {{ $doc->document_type ?? 'NFC-E' }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-slate-600" style="padding: 1rem;">
                                {{ $doc->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 font-mono font-medium text-slate-700" style="padding: 1rem; font-family: monospace;">
                                @if($doc->sale)
                                    {{ new App\Modules\Core\ValueObjects\Money($doc->sale->total_cents) }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td class="p-4" style="padding: 1rem;">
                                @php
                                    $bg = '#f1f5f9'; $color = '#475569'; $border = '#cbd5e1';
                                    if($doc->status === 'AUTORIZADO') { $bg = '#ecfdf5'; $color = '#047857'; $border = '#a7f3d0'; }
                                    if($doc->status === 'CONTINGENCIA_OFFLINE') { $bg = '#fffbeb'; $color = '#b45309'; $border = '#fde68a'; }
                                    if($doc->status === 'CANCELADO') { $bg = '#fef2f2'; $color = '#b91c1c'; $border = '#fecaca'; }
                                @endphp
                                <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; font-weight: bold; background: {{ $bg }}; color: {{ $color }}; border: 1px solid {{ $border }};">
                                    {{ $doc->status }}
                                </span>
                                @if($doc->protocol_number)
                                    <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.25rem; font-family: monospace;">Prot: {{ $doc->protocol_number }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-right" style="padding: 1rem; text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                    @if($doc->status !== 'CANCELADO')
                                    <button type="button" onclick="confirmCancel({{ $doc->id }})" class="btn text-danger transition-colors" style="background: transparent; border: 1px solid transparent; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: bold; cursor: pointer;" title="Cancelar Nota">
                                        <i class="fa fa-ban text-danger"></i>
                                    </button>
                                    @endif
                                </div>
                                
                                <form id="cancel-form-{{ $doc->id }}" action="{{ route('fiscal.records.cancel', $doc->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="reason" id="cancel-reason-{{ $doc->id }}" value="Erro operacional local">
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 4rem; text-align: center; color: #94a3b8;">
                                <div style="font-size: 4rem; margin-bottom: 1rem;">🧾</div>
                                <p style="font-weight: bold;">Nenhum documento fiscal registrado ainda.</p>
                            </td>
                        </tr>
                    @endforelse
                </x-slot>
            </x-ui.table>
            
            @if($documents->hasPages())
                <div class="p-4" style="padding: 1rem; border-top: 1px solid #e2e8f0; background: #f8fafc;">
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
