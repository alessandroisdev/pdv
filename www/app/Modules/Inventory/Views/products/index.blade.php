<x-layouts.app>
    <x-slot:title>Controle de Estoque | Produtos</x-slot:title>

    <div class="p-6">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Módulo de Estoque</h2>
                <p class="text-slate-500">Gerenciamento de Produtos, Variações e Categorias.</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg font-semibold shadow-sm transition-colors">
                    Gerenciar Categorias
                </button>
                <a href="{{ route('inventory.products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold shadow shadow-indigo-200 transition-colors inline-block">
                    <i class="fa fa-plus mr-2"></i> Novo Produto
                </a>
            </div>
        </div>

        <!-- Filtros Básicos (Visual) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm mb-6 flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <i class="fa fa-search absolute left-3 top-3 text-slate-400"></i>
                <input type="text" placeholder="Buscar por Nome, SKU ou Código de Barras..." class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <select class="w-full md:w-64 bg-slate-50 border border-slate-200 text-slate-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">Todas as Categorias</option>
            </select>
            <button class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-6 py-2 rounded-lg font-semibold transition-colors whitespace-nowrap">
                Filtrar
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-700">Lista de Produtos Ativos</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
                            <th class="p-4 font-semibold w-24">SKU</th>
                            <th class="p-4 font-semibold">Nome do Produto</th>
                            <th class="p-4 font-semibold">Categoria</th>
                            <th class="p-4 font-semibold">Preço Cust.</th>
                            <th class="p-4 font-semibold">Preço Vend.</th>
                            <th class="p-4 font-semibold text-center">Estoque Atual</th>
                            <th class="p-4 font-semibold text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4">
                                    <span class="bg-slate-100 text-slate-600 font-mono text-xs px-2 py-1 rounded border border-slate-200">{{ $product->sku ?? '---' }}</span>
                                </td>
                                <td class="p-4 font-bold text-slate-800">{{ $product->name }}</td>
                                <td class="p-4 text-slate-600">{{ $product->category->name ?? 'Sem Categoria' }}</td>
                                <td class="p-4 text-slate-500 tabular-nums">{{ clone $product->cost_price }}</td>
                                <td class="p-4 text-indigo-700 font-bold tabular-nums">{{ clone $product->sale_price }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $product->current_stock > 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $product->current_stock }} Und
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <button class="text-indigo-600 hover:bg-indigo-50 px-3 py-1 rounded border border-indigo-200 text-sm font-semibold transition-colors">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center">
                                    <div class="text-5xl text-slate-200 mb-4">
                                        <i class="fa fa-box-open"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-700 mb-1">Nenhum produto cadastrado!</h4>
                                    <p class="text-slate-500 text-sm">Comece cadastrando suas categorias e produtos clicando no botão acima.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
