<x-layouts.app>
    <x-slot:title>Auditoria de Estoque | {{ $product->name }}</x-slot:title>

    <div class="mb-4">
        <a href="{{ route('inventory.products.index') }}" class="text-light fw-semibold" style="text-decoration: none; font-size: 0.85rem;">&larr; Voltar para a lista</a>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1rem;">
            <div>
                <h1 class="text-primary fw-bold" style="font-size: 1.75rem;">Livro-Razão de Estoque</h1>
                <p class="text-light" style="margin-top: 0.25rem;">{{ $product->name }} (SKU: {{ $product->sku }})</p>
            </div>
            
            <div style="background-color: {{ $product->current_stock > 10 ? '#ecfdf5' : '#fff1f2' }}; color: {{ $product->current_stock > 10 ? '#047857' : '#be123c' }}; border: 1px solid {{ $product->current_stock > 10 ? '#a7f3d0' : '#fecaca' }}; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-align: center;">
                <div style="font-size: 0.75rem; font-weight: bold; text-transform: uppercase; margin-bottom: 0.25rem;">Saldo Físico Atual</div>
                <div style="font-size: 2rem; font-weight: 900; line-height: 1;">{{ $product->current_stock }}</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; color: #047857; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.35rem; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <style>
            @media (min-width: 1024px) {
                .stock-grid { grid-template-columns: 350px 1fr !important; }
            }
        </style>
        
        <div class="stock-grid" style="display: grid; gap: 2rem; grid-template-columns: 1fr;">
            
            <!-- Painel Esquerdo: CRUD / Novo Ajuste -->
            <div>
                <div class="card shadow-sm border-0 bg-white" style="border-radius: 0.75rem; overflow: hidden; position: sticky; top: 1.5rem;">
                    <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="font-size: 1rem; font-weight: bold; color: #1e293b; margin: 0;"><i class="fa fa-boxes"></i> Novo Ajuste Contábil</h3>
                    </div>
                    
                    <form action="{{ route('inventory.products.stock.store', $product) }}" method="POST" style="padding: 1.5rem;">
                        @csrf
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.75rem;">Operação Lógica</label>
                            
                            <div style="display: flex; gap: 1rem;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                    <input type="radio" name="operation" value="in" required style="width: 1.25rem; height: 1.25rem; accent-color: #10b981;">
                                    <span style="color: #047857; font-weight: bold;">Entrada Farta (+)</span>
                                </label>

                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                    <input type="radio" name="operation" value="out" required style="width: 1.25rem; height: 1.25rem; accent-color: #ef4444;">
                                    <span style="color: #b91c1c; font-weight: bold;">Dar Baixa (-)</span>
                                </label>
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Volume Específico (UN/KG)</label>
                            <input type="number" name="quantity" min="1" step="1" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1.25rem; text-align: center; font-weight: bold;" placeholder="Ex: 5">
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Justificativa Regulatória (Obrigatório)</label>
                            <input type="text" name="transaction_motive" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;" placeholder="Ex: Inventário Anual, Produto em Quebra...">
                        </div>

                        <button type="submit" class="btn shadow" style="width: 100%; background: #4f46e5; border: none; color: white; padding: 1rem; font-weight: bold; border-radius: 0.5rem; cursor: pointer; font-size: 1rem; transition: transform 0.2s;">
                            Efetivar Registro Central
                        </button>
                    </form>
                </div>
            </div>

            <!-- Painel Direito: Histórico -->
            <div>
                <div class="card shadow-sm border-0 bg-white" style="border-radius: 0.75rem; overflow: hidden; min-height: 500px;">
                    <div style="padding: 1.5rem; background: white; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 1rem; font-weight: bold; color: #1e293b; margin: 0;"><i class="fa fa-history" style="color: #94a3b8; margin-right: 0.5rem;"></i> Histórico Crítico de Eventos</h3>
                    </div>

                    <div style="padding: 1rem; overflow-x: auto;">
                        <table class="display responsive nowrap w-100" id="stock-movements-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.85rem;">
                                    <th style="padding: 1rem; text-align: left;">Data/Hora</th>
                                    <th style="padding: 1rem; text-align: left;">Módulo Motor</th>
                                    <th style="padding: 1rem; text-align: left;">Justificativa / Motivo</th>
                                    <th style="padding: 1rem; text-align: left;">Assinatura Contábil</th>
                                    <th style="padding: 1rem; text-align: right;">Mutação Fís.</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const initStockTable = () => {
                            if (typeof window.AppServerTable !== 'function') {
                                setTimeout(initStockTable, 100);
                                return;
                            }
                            // Usando caminho literal fixo sem "route()" do blade devido a bugs de cache:
                            new window.AppServerTable('#stock-movements-table', '/estoque/produtos/{{ $product->id }}/estoque/datatable', [
                                { data: 'm_data', name: 'created_at', searchable: false },
                                { data: 'modulo', name: 'type', searchable: false },
                                { data: 'motivo', name: 'transaction_motive', searchable: true },
                                { data: 'actor', searchable: false, orderable: false },
                                { data: 'mutacao', name: 'quantity', searchable: false, className: 'text-right' }
                            ], [[0, 'desc']]); // Fallback ordena criado recentemente
                        };
                        initStockTable();
                    });
                </script>
            </div>

        </div>
    </div>
</x-layouts.app>
