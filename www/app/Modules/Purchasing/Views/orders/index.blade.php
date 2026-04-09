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
            <table class="display responsive nowrap w-100" id="purchasing-orders-table" style="width: 100%; text-align: left; border-collapse: collapse;">
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
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const initOrderTable = () => {
                    if (typeof window.AppServerTable !== 'function') {
                        setTimeout(initOrderTable, 100);
                        return;
                    }
                    new window.AppServerTable('#purchasing-orders-table', '{{ route('purchasing.orders.datatable') }}', [
                        { data: 'pedido', searchable: true, orderable: true, name: 'id' },
                        { data: 'fornecedor', searchable: false, orderable: false },
                        { data: 'status', searchable: false, orderable: false },
                        { data: 'qtd', searchable: false, orderable: false },
                        { data: 'total', searchable: false, orderable: false },
                        { data: 'acoes', searchable: false, orderable: false, className: 'text-right' }
                    ], [[0, 'desc']]); // Fallback default via JS id 0. (invoice / id)
                };
                initOrderTable();
            });
            
            function confirmReceive(orderId) {
                if (window.Swal) {
                    Swal.fire({
                        title: 'Confirmar Entrada de Nota?',
                        text: "O estoque será incrementado imediatamente e as contas a pagar registradas na tesouraria.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#059669',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Sim, Registrar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('receive-form-' + orderId).submit();
                        }
                    });
                } else if(confirm('Registrar entrada dos itens selecionados?')) {
                    document.getElementById('receive-form-' + orderId).submit();
                }
            }
        </script>
        </div>
    </div>
</x-layouts.app>
