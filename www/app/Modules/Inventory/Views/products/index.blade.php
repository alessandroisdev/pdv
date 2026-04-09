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
        
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                            <th style="padding: 1rem;">SKU</th>
                            <th style="padding: 1rem;">Nome do Produto</th>
                            <th style="padding: 1rem;">Categoria</th>
                            <th style="padding: 1rem;">Preço Cust.</th>
                            <th style="padding: 1rem;">Preço Vend.</th>
                            <th style="padding: 1rem; text-align: center;">Estoque Atual</th>
                            <th style="padding: 1rem; text-align: right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                                <td style="padding: 1rem;">
                                    <span style="background-color: #f1f5f9; color: #64748b; font-family: monospace; font-size: 0.75rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">{{ $product->sku ?? '---' }}</span>
                                </td>
                                <td style="padding: 1rem; font-weight: bold; color: #1e293b;">{{ $product->name }}</td>
                                <td style="padding: 1rem; color: #64748b;">{{ $product->category->name ?? 'Sem Categoria' }}</td>
                                <td style="padding: 1rem; color: #64748b; font-family: tabular-nums;">{{ clone $product->cost_price }}</td>
                                <td style="padding: 1rem; color: #455073; font-weight: bold; font-family: tabular-nums;">{{ clone $product->sale_price }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; {{ $product->current_stock > 10 ? 'background-color: #d1fae5; color: #047857;' : 'background-color: #ffe4e6; color: #be123c;' }}">
                                        {{ $product->current_stock }} Und
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: right; display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('inventory.products.stock', $product) }}" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; text-decoration: none; border-color: #10b981; color: #047857;">
                                        <i class="fa fa-boxes"></i> Estoque
                                    </a>
                                    <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; text-decoration: none;">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 3rem; text-align: center;">
                                    <div style="font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem;">
                                        <i class="fa fa-box-open"></i>
                                    </div>
                                    <h4 style="font-size: 1.125rem; font-weight: bold; color: #455073; margin-bottom: 0.25rem;">Nenhum produto cadastrado!</h4>
                                    <p style="color: #64748b; font-size: 0.875rem;">Comece cadastrando suas categorias e produtos clicando no botão acima.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div style="padding: 1rem; border-top: 1px solid #e2e8f0; background-color: #f8fafc;">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
