<x-layouts.app>
    <div>
        <div class="flex justify-between items-center" style="margin-bottom: 1.5rem;">
            <div>
                <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Tesouraria (Contas a Pagar / Receber)</h2>
                <p class="text-light" style="margin-top: 0.25rem;">Gestão de Fornecedores, Faturas e Boletos de Devedores.</p>
            </div>
            <div>
                <button onclick="document.getElementById('modalLancamento').style.display = 'flex'" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5;">
                    <i class="fa fa-plus"></i> Lançar Título
                </button>
                
                <!-- Modal de Novo Título -->
                <div id="modalLancamento" style="display: none; position: fixed; inset: 0; z-index: 50; overflow-y: auto; background: rgba(15, 23, 42, 0.75); align-items: center; justify-content: center; padding: 1rem;">
                    <div style="position: relative; background: white; border-radius: 0.75rem; text-align: left; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); width: 100%; max-width: 32rem;">
                        <form action="{{ route('finance.installments.store') }}" method="POST">
                            @csrf
                            <div style="padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">Cadastrar Novo Título</h3>
                                <button type="button" onclick="document.getElementById('modalLancamento').style.display = 'none'" style="background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.25rem;"><i class="fa fa-times"></i></button>
                            </div>
                            <div style="padding: 1.5rem; display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Tipo de Título</label>
                                    <select name="type" required class="form-control" style="width: 100%;">
                                        <option value="PAYABLE">Conta a Pagar (Despesa/Fornecedor)</option>
                                        <option value="RECEIVABLE">Conta a Receber (Receita/Fiado)</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Descrição</label>
                                    <input type="text" name="description" required placeholder="Ex: Boleto Coca-Cola Mês 04" class="form-control" style="width: 100%;">
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Valor (R$)</label>
                                        <input type="number" step="0.01" min="0.01" name="amount_total" required placeholder="150.00" class="form-control" style="width: 100%;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem;">Vencimento</label>
                                        <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="form-control" style="width: 100%;">
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <button type="button" onclick="document.getElementById('modalLancamento').style.display = 'none'" class="btn btn-outline" style="border: none; color: #475569;">Cancelar</button>
                                <button type="submit" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5;">Salvar Título</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores Críticos -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card" style="border: 1px solid #fecdd3; background: #fff1f2;">
                <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                    <div style="font-weight: bold; color: #e11d48; margin-bottom: 0.25rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;"><i class="fa fa-exclamation-triangle"></i> Contas a Pagar Vencidas</div>
                    <div style="font-size: 1.875rem; font-weight: 900; color: #be123c;">{{ $overduePayablesCount }}</div>
                    <div style="font-size: 0.75rem; color: #f43f5e; margin-top: 0.5rem;">Correm juros e multa</div>
                </div>
            </div>
            <div class="card" style="border: 1px solid #a7f3d0; background: #ecfdf5;">
                <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                    <div style="font-weight: bold; color: #047857; margin-bottom: 0.25rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;"><i class="fa fa-clock"></i> Inadimplência a Receber</div>
                    <div style="font-size: 1.875rem; font-weight: 900; color: #065f46;">{{ $overdueReceivablesCount }}</div>
                    <div style="font-size: 0.75rem; color: #059669; margin-top: 0.5rem;">Fiados / Boletos Vencidos Faltando Cobrar</div>
                </div>
            </div>
        </div>

        <x-ui.card>
            <!-- Filtros Superiores -->
            <div style="padding: 1rem; border-bottom: 1px solid #f1f5f9; background: #f8fafc; display: flex; gap: 1rem;">
                <a href="{{ route('finance.installments.index') }}" class="btn {{ !request('type') ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.25rem 1rem; border-radius: 999px;">Todos</a>
                <a href="{{ route('finance.installments.index', ['type' => 'PAYABLE']) }}" class="btn {{ request('type') == 'PAYABLE' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.25rem 1rem; border-radius: 999px; {{ request('type') == 'PAYABLE' ? 'background: #e11d48; border-color: #e11d48;' : '' }}">Só A Pagar</a>
                <a href="{{ route('finance.installments.index', ['type' => 'RECEIVABLE']) }}" class="btn {{ request('type') == 'RECEIVABLE' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.25rem 1rem; border-radius: 999px; {{ request('type') == 'RECEIVABLE' ? 'background: #10b981; border-color: #10b981;' : '' }}">Só A Receber</a>
            </div>

            <!-- Tabela Server Side TS -->
            <div style="overflow-x: auto; padding: 1.5rem;">
                <table class="display responsive nowrap w-100" id="finance-installments-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.875rem;">
                            <th style="padding: 1rem; text-align: left;">Tipo</th>
                            <th style="padding: 1rem; text-align: left;">Descrição do Título</th>
                            <th style="padding: 1rem; text-align: left;">Valor</th>
                            <th style="padding: 1rem; text-align: left;">Vencimento</th>
                            <th style="padding: 1rem; text-align: left;">Status / Pagamento</th>
                            <th style="padding: 1rem; text-align: right;">Ação</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const initInstTable = () => {
                        if (typeof window.AppServerTable !== 'function') {
                            setTimeout(initInstTable, 100);
                            return;
                        }
                        
                        // Capturando os Query Params no frontend pra puxar o Filter (A PAGAR, RECEBER, TODOS)
                        const urlParams = new URLSearchParams(window.location.search);
                        const typeFilter = urlParams.get('type') || '';

                        const ajaxUrl = '{{ route('finance.installments.datatable') }}' + (typeFilter ? '?type=' + typeFilter : '');

                        new window.AppServerTable('#finance-installments-table', ajaxUrl, [
                            { data: 'tipo', name: 'type', searchable: false },
                            { data: 'descricao', name: 'description', searchable: true },
                            { data: 'valor', name: 'amount_cents', searchable: false, orderable: false },
                            { data: 'vencimento', name: 'due_date', searchable: false },
                            { data: 'status', name: 'status', searchable: false, orderable: false },
                            { data: 'acoes', searchable: false, orderable: false, className: 'text-right' }
                        ], [[3, 'asc']]); // Default: Ordenar por Vencimento ASC
                    };
                    initInstTable();
                });
            </script>
        </x-ui.card>
    </div>
</x-layouts.app>
