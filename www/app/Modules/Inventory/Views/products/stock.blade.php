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
                    <!-- TABS: Ajuste vs Lote -->
                    <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; gap: 1rem;">
                        <button onclick="document.getElementById('ajuste-form').style.display='block'; document.getElementById('lote-form').style.display='none'; this.style.color='#4f46e5'; this.style.borderBottom='2px solid #4f46e5';" style="background: none; border: none; padding-bottom: 0.5rem; font-weight: bold; color: #4f46e5; border-bottom: 2px solid #4f46e5; cursor: pointer;">
                            <i class="fa fa-balance-scale"></i> Ajuste Físico
                        </button>
                        <button onclick="document.getElementById('ajuste-form').style.display='none'; document.getElementById('lote-form').style.display='block'; this.previousElementSibling.style.color='#64748b'; this.previousElementSibling.style.borderBottom='none'; this.style.color='#10b981'; this.style.borderBottom='2px solid #10b981';" style="background: none; border: none; padding-bottom: 0.5rem; font-weight: bold; color: #64748b; cursor: pointer;">
                            <i class="fa fa-truck-loading"></i> Lote WMS
                        </button>
                    </div>

                    @if($errors->any())
                        <div style="margin: 1rem 1.5rem; padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 0.5rem; font-size: 0.85rem; font-weight: bold;">
                            @foreach ($errors->all() as $error)
                                <div><i class="fa fa-exclamation-triangle"></i> {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- FORM: Ajuste Simples -->
                    <form id="ajuste-form" action="{{ route('inventory.products.stock.store', $product) }}" method="POST" style="padding: 1.5rem;">
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
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Justificativa Regulatória</label>
                            <input type="text" name="transaction_motive" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;" placeholder="Ex: Inventário Anual...">
                        </div>
                        <button type="submit" class="btn shadow" style="width: 100%; background: #4f46e5; border: none; color: white; padding: 1rem; font-weight: bold; border-radius: 0.5rem;">
                            Efetivar Ajuste
                        </button>
                    </form>

                    <!-- FORM: Lote WMS -->
                    <form id="lote-form" action="{{ route('inventory.products.stock.batch', $product) }}" method="POST" style="padding: 1.5rem; display: none;">
                        @csrf
                        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <div style="font-size: 0.75rem; font-weight: bold; color: #047857; text-transform: uppercase;">Atenção Logística FEFO</div>
                            <div style="font-size: 0.85rem; color: #065f46; margin-top: 0.25rem;">A injeção de Lote fiscal bloqueará entrada de produtos cuja validade seja retroativa a hoje.</div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Número do Lote</label>
                                <input type="text" name="batch_number" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-weight: bold; font-family: monospace;" placeholder="L-99801">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Vencimento</label>
                                <input type="date" name="expires_at" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;">
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Quantidade Entregue</label>
                            <input type="number" name="quantity" min="1" step="1" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1.25rem; text-align: center; font-weight: bold;" placeholder="Ex: 50">
                        </div>

                        <button type="submit" class="btn shadow" style="width: 100%; background: #10b981; border: none; color: white; padding: 1rem; font-weight: bold; border-radius: 0.5rem;">
                            Registrar Entrada de Lote
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
                                    <th style="padding: 1rem; text-align: left;">Rastreabilidade WMS</th>
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
                            new window.AppServerTable('#stock-movements-table', '/estoque/produtos/{{ $product->id }}/estoque/datatable', [
                                { data: 'm_data', name: 'created_at', searchable: false },
                                { data: 'modulo', name: 'type', searchable: false },
                                { data: 'motivo', name: 'transaction_motive', searchable: true },
                                { 
                                    data: 'actor', searchable: false, orderable: false,
                                    render: function(data, type, row) {
                                        // A API original passa o actor (Usuário). Se quisermos mostrar o lote dinamicamente sem alterar o C#, a gente extrai na source ou sobrescreve.
                                        // Wait, we need to modify the datatable backend to return 'batch_tag'. 
                                        // For now, let's keep the raw actor text, but ideally we add Lote.
                                        return data; 
                                    }
                                },
                                { data: 'mutacao', name: 'quantity', searchable: false, className: 'text-right' }
                            ], [[0, 'desc']]);
                        };
                        initStockTable();
                    });
                </script>
            </div>

        </div>
    </div>
</x-layouts.app>
