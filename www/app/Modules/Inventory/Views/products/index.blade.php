<x-layouts.app>
    <x-slot:title>Controle de Estoque | Produtos</x-slot:title>

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-primary fw-bold" style="font-size: 1.75rem;">Módulo de Estoque</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Gerenciamento de Produtos e Categorias.</p>
        </div>
        <div>
            <button class="btn btn-outline" style="margin-right: 0.5rem;">Gerenciar Categorias</button>
            <a href="{{ route('inventory.products.create') }}" class="btn btn-primary" style="text-decoration: none;">+ Novo Produto</a>
        </div>
    </div>

    <!-- Filtros Básicos (Visual) -->
    <x-ui.card class="mb-4">
        <div class="flex gap-4 items-center">
            <input type="text" placeholder="Buscar por Nome, SKU ou Código de Barras..." style="flex: 1; padding: 0.6rem 1rem; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; outline: none;">
            <select style="padding: 0.6rem 2rem 0.6rem 1rem; border: 1px solid #e2e8f0; border-radius: 6px; font-family: inherit; background: white;">
                <option value="">Todas as Categorias</option>
            </select>
            <button class="btn btn-primary" style="background: #e2e8f0; color: #455073;">Filtrar</button>
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>Lista de Produtos Ativos</x-slot:header>
        
        <x-ui.table>
            <x-slot:head>
                <th>SKU</th>
                <th>Nome do Produto</th>
                <th>Categoria</th>
                <th>Preço Cust.</th>
                <th>Preço Vend.</th>
                <th>Estoque Atual</th>
                <th style="text-align: right;">Ações</th>
            </x-slot:head>
            
            @forelse($products as $product)
                <tr>
                    <td><span style="font-family: monospace; background: #f1f5f9; padding: 0.2rem 0.4rem; border-radius: 4px;">{{ $product->sku ?? '---' }}</span></td>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'Sem Categoria' }}</td>
                    <td>R$ {{ number_format($product->cost_price->getCents() / 100, 2, ',', '.') }}</td>
                    <td class="text-contrast fw-bold">R$ {{ number_format($product->sale_price->getCents() / 100, 2, ',', '.') }}</td>
                    <td>
                        <span style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.8rem; background: {{ $product->current_stock > 10 ? '#dcfce7; color: #166534;' : '#fee2e2; color: #991b1b;' }}">
                            {{ $product->current_stock }} Und
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button class="btn" style="padding: 0.25rem 0.5rem; background: transparent; border: 1px solid #e2e8f0; color: #455073; font-size: 0.75rem;">Editar</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem;">
                        <div style="color: #64748b; margin-bottom: 1rem;">
                            <!-- Placeholder SVG block -->
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="margin: 0 auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h4 style="font-size: 1.1rem; color: #455073; margin-bottom: 0.5rem;">Nenhum produto cadastrado!</h4>
                        <p style="font-size: 0.9rem;">Comece cadastrando suas categorias e produtos clicando no botão acima.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
        
        <div style="margin-top: 1.5rem;">
            {{ $products->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
