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
            <div style="overflow-x: auto; padding: 1.5rem;">
                <table class="display responsive nowrap w-100" id="fiscal-records-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                            <th style="padding: 1rem; text-align: left;">ID / Venda</th>
                            <th style="padding: 1rem; text-align: left;">Modelo</th>
                            <th style="padding: 1rem; text-align: left;">Emissão</th>
                            <th style="padding: 1rem; text-align: left;">Valor</th>
                            <th style="padding: 1rem; text-align: left;">Status / Recibo</th>
                            <th style="padding: 1rem; text-align: right;">Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const initFiscalTable = () => {
                        if (typeof window.AppServerTable !== 'function') {
                            setTimeout(initFiscalTable, 100);
                            return;
                        }
                        
                        new window.AppServerTable('#fiscal-records-table', '{{ route('fiscal.records.datatable') }}', [
                            { data: 'id_venda', name: 'id', searchable: true },
                            { data: 'modelo', name: 'document_type', searchable: false },
                            { data: 'emissao', name: 'created_at', searchable: false },
                            { data: 'valor', searchable: false, orderable: false },
                            { data: 'status', name: 'status', searchable: true },
                            { data: 'acoes', searchable: false, orderable: false, className: 'text-right' }
                        ], [[0, 'desc']]); // Ordenar por ID descendente
                    };
                    initFiscalTable();
                });
            </script>
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
