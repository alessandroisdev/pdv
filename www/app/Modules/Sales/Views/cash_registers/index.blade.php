<x-layouts.app>
    <x-slot:title>Vendas | Caixas Operacionais</x-slot:title>

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-primary fw-bold" style="font-size: 1.75rem;">Gestão de Caixas (PDV)</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Monitoramento de turnos e aberturas de caixa em tempo real.</p>
        </div>
        <div>
            <a href="{{ route('sales.cash_registers.export') }}" class="btn btn-outline" style="background-color: white;">
                <i class="fa fa-file-excel"></i> Relatório de Fechamentos
            </a>
        </div>
    </div>

    <!-- Filtros Básicos -->
    <x-ui.card class="mb-4">
        <div class="flex gap-4 items-center">
            <select class="form-control" style="width: auto;">
                <option value="">Status: Todos</option>
                <option value="open">Abertos</option>
                <option value="closed">Fechados</option>
            </select>
            <button class="btn btn-primary" style="background: #e2e8f0; color: #455073; border-color: #e2e8f0;">Aplicar Filtro</button>
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>Histórico de Status dos Caixas</x-slot:header>
        
        <div style="overflow-x: auto;">
            <table class="display responsive nowrap w-100" id="sales-registers-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                        <th style="padding: 1rem; text-align: left;">Turno #ID</th>
                        <th style="padding: 1rem; text-align: left;">Operador de Frente</th>
                        <th style="padding: 1rem; text-align: left;">Status Atual</th>
                        <th style="padding: 1rem; text-align: left;">Abertura</th>
                        <th style="padding: 1rem; text-align: left;">Fundo de Troco</th>
                        <th style="padding: 1rem; text-align: left;">Fechamento</th>
                        <th style="padding: 1rem; text-align: right;">Opções</th>
                    </tr>
                </thead>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const initRegTable = () => {
                    if (typeof window.AppServerTable !== 'function') {
                        setTimeout(initRegTable, 100);
                        return;
                    }
                    
                    // Tratamento do Filtro Manual Superior
                    let currentStatus = '';
                    const selectStatus = document.querySelector('select.form-control');
                    const btnFilter = document.querySelector('.btn-primary');
                    
                    let tableInstance = new window.AppServerTable('#sales-registers-table', '{{ route('sales.cash_registers.datatable') }}', [
                        { data: 'turno', name: 'id', searchable: false },
                        { data: 'operador', searchable: false, orderable: false },
                        { data: 'status', searchable: false, orderable: false },
                        { data: 'abertura', searchable: false, orderable: false },
                        { data: 'fundo', searchable: false, orderable: false },
                        { data: 'fechamento', searchable: false, orderable: false },
                        { data: 'opcoes', searchable: false, orderable: false, className: 'text-right' }
                    ], [[0, 'desc']]); // Ordernar ID Desc

                    if (btnFilter) {
                        btnFilter.addEventListener('click', () => {
                            currentStatus = selectStatus.value;
                            // Atualizar URL AJAX
                            const newUrl = '{{ route('sales.cash_registers.datatable') }}' + (currentStatus ? '?status=' + currentStatus : '');
                            tableInstance.dtInstance.ajax.url(newUrl).load();
                        });
                    }
                };
                initRegTable();
            });
        </script>
    </x-ui.card>
</x-layouts.app>
