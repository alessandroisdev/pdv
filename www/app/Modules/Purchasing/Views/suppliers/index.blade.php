<x-layouts.app>
    <x-slot:title>Compras | Fornecedores</x-slot:title>

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-primary fw-bold" style="font-size: 1.75rem;">Fornecedores</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Gestão de parceiros e reposição de estoque.</p>
        </div>
        <a href="{{ route('purchasing.suppliers.create') }}" class="btn btn-primary" style="text-decoration:none;">+ Cadastrar Fornecedor</a>
    </div>

    <!-- Filtros Básicos -->
    <x-ui.card class="mb-4">
        <div class="flex gap-4 items-center">
            <input type="text" placeholder="Buscar por Nome ou CNPJ..." style="flex: 1; padding: 0.6rem 1rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
            <button class="btn btn-primary" style="background: #e2e8f0; color: #455073;">Filtrar / Buscar</button>
        </div>
    </x-ui.card>

    <x-ui.card>
        <x-slot:header>Lista de Fornecedores Ativos</x-slot:header>
        
        <x-ui.table>
            <x-slot:head>
                <th>Código</th>
                <th>Razão Social / Nome</th>
                <th>Documento (CNPJ)</th>
                <th>E-Mail Comercial</th>
                <th>Telefone</th>
                <th style="text-align: right;">Opções</th>
            </x-slot:head>
            
            @forelse($suppliers as $supplier)
                <tr>
                    <td>#{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="fw-semibold">{{ $supplier->name }}</td>
                    <td>{{ $supplier->document ?? '---' }}</td>
                    <td>{{ $supplier->email ?? '---' }}</td>
                    <td>{{ $supplier->phone ?? '---' }}</td>
                    <td style="text-align: right;">
                        <button class="btn" style="padding: 0.25rem 0.5rem; border: 1px solid #e2e8f0; color: #455073; font-size: 0.75rem;">Consultar</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem;">
                        <div style="color: #64748b; margin-bottom: 1rem;">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="margin: 0 auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 style="font-size: 1.1rem; color: #455073; margin-bottom: 0.5rem;">Nenhum fornecedor registrado!</h4>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
        
        <div style="margin-top: 1.5rem;">
            {{ $suppliers->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
