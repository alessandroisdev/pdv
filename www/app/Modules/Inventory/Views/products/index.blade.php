<x-layouts.app>
    <x-slot:title>Controle de Estoque | Produtos</x-slot:title>

    <!-- Header do Modulo -->
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-primary">Módulo de Estoque</h2>
            <p class="text-light">Gerenciamento de Produtos, Variações e Categorias.</p>
        </div>
        <div class="flex" style="gap: 10px;">
            <a href="{{ route('inventory.categories.index') }}" class="btn btn-outline">
                <i class="fa fa-tags"></i> Gerenciar Categorias
            </a>
            <a href="{{ route('inventory.products.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> &nbsp; Novo Produto
            </a>
        </div>
    </div>

    <!-- Filtros Básicos (Visual) -->
    <div class="card mb-4">
        <div class="card-body flex items-center justify-between" style="gap: 1rem;">
            <div class="form-group mb-0" style="flex: 1; position: relative;">
                <input type="text" placeholder="Buscar por Nome, SKU ou Código de Barras..." class="form-control" style="padding-left: 2.5rem;">
                <i class="fa fa-search" style="position: absolute; left: 15px; top: 12px; color: #a0aec0;"></i>
            </div>
            <div class="form-group mb-0" style="width: 300px;">
                <select class="form-control">
                    <option value="">Todas as Categorias</option>
                </select>
            </div>
            <button class="btn btn-primary" style="align-self: flex-start;">
                Filtrar
            </button>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card">
        <div class="card-header">
            <h3>Lista de Produtos Ativos</h3>
        </div>
        
        <div class="card-body" style="padding: 1.5rem;">
            <div style="overflow-x: auto;">
                <table id="products-table" class="display responsive nowrap w-100" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                            <th style="padding: 1rem; width:100px;">SKU</th>
                            <th style="padding: 1rem;">NOME DO PRODUTO</th>
                            <th style="padding: 1rem;">CATEGORIA</th>
                            <th style="padding: 1rem;">CUSTO</th>
                            <th style="padding: 1rem;">VENDA</th>
                            <th style="padding: 1rem; text-align: center;">ESTOQUE</th>
                            <th style="padding: 1rem; text-align: right; width:150px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <!-- O tbody será desenhado por TS -->
                </table>
            </div>
        </div>
    </div>

    <!-- Inicializador Server-Side Datatable -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initDatatable = () => {
                if (typeof window.AppServerTable !== 'function') {
                    setTimeout(initDatatable, 100);
                    return;
                }
                
                new window.AppServerTable('#products-table', '{{ route('inventory.products.datatable') }}', [
                    { 
                        data: 'sku', searchable: true, 
                        render: function(data) { return `<span style="background-color:#f1f5f9; color:#64748b; font-family:monospace; font-size:0.75rem; padding:2px 6px; border-radius:4px; border:1px solid #e2e8f0;">${data || '---'}</span>`; }
                    },
                    { data: 'name', searchable: true, className: "font-bold text-slate-800" },
                    { data: 'category_name', searchable: false, orderable: false, className: "text-slate-500" },
                    { data: 'cost_price_formatted', searchable: false, orderable: false, className: "text-slate-500 tabular-nums" },
                    { data: 'sale_price_formatted', searchable: false, orderable: false, className: "font-bold tabular-nums", style: "color: #455073;" },
                    { data: 'virtual_stock', searchable: false, className: "text-center" },
                    { data: 'acoes', searchable: false, orderable: false, className: "text-right" }
                ], [[1, 'asc']]); // Ordenar por NOME by default
            };
            initDatatable();
        });
    </script>
    </div>
</x-layouts.app>
