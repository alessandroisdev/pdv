<x-layouts.app>
    <x-slot:title>Motor Tributário | Configurações</x-slot:title>

    <div class="mb-4 d-flex justify-content-between align-items-end" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 2rem;">
        <div>
            <h1 class="text-primary fw-bold" style="font-size: 1.75rem;"><i class="fa fa-percent me-2 text-slate-400"></i> Motor Tributário</h1>
            <p class="text-light" style="margin-top: 0.25rem;">Gestão Dinâmica de Alíquotas e Enquadramento Fiscal.</p>
        </div>
        <div style="background: #e0e7ff; color: #4338ca; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid #c7d2fe; text-align: center; font-weight: bold;">
            <i class="fa fa-shield-alt"></i> Ambiente Isolado do Contador
        </div>
    </div>

    @if(session('success'))
        <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; color: #047857; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.35rem; font-weight: bold;">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.35rem; font-weight: bold;">
            @foreach($errors->all() as $error)
                <div><i class="fa fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <style>
            @media (min-width: 1024px) {
                .tax-grid { grid-template-columns: 350px 1fr !important; }
            }
        </style>
        
        <div class="tax-grid" style="display: grid; gap: 2rem; grid-template-columns: 1fr;">
            
            <!-- Painel Esquerdo: CRUD / Novo Enquadramento -->
            <div>
                <div class="card shadow-sm border-0 bg-white" style="border-radius: 0.75rem; overflow: hidden; position: sticky; top: 1.5rem;">
                    <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="font-size: 1rem; font-weight: bold; color: #1e293b; margin: 0;"><i class="fa fa-plus-circle text-indigo-500"></i> Nova Regra Automática</h3>
                    </div>
                    
                    <form action="{{ route('fiscal.settings.taxes.store') }}" method="POST" style="padding: 1.5rem;">
                        @csrf
                        
                        <div style="margin-bottom: 1.25rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Regime da Empresa</label>
                            <select name="fiscal_regime" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: #f8fafc; font-weight: bold;">
                                <option value="SIMPLES_NACIONAL">Simples Nacional</option>
                                <option value="LUCRO_PRESUMIDO">Lucro Presumido</option>
                                <option value="LUCRO_REAL">Lucro Real</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 1.25rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">NCM Específico <span style="font-size: 0.7rem; color: #94a3b8; font-weight: normal;">(Deixe vazio para Regra Geral)</span></label>
                            <input type="text" name="ncm" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-family: monospace;" placeholder="Ex: 2202.99.00">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">CST/CSOSN</label>
                                <input type="text" name="cst_csosn" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-family: monospace;" placeholder="Ex: 102">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">CFOP</label>
                                <input type="text" name="cfop" required class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-family: monospace;" placeholder="Ex: 5102">
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                            <h4 style="font-size: 0.75rem; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 1rem;">Alíquotas (%)</h4>
                            <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.85rem; font-weight: bold; color: #475569;">ICMS</span>
                                    <input type="number" step="0.01" min="0" name="icms_rate" required class="form-control text-right py-1 px-2" style="width: 100px; border: 1px solid #cbd5e1; border-radius: 0.25rem;" placeholder="0,00">
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.85rem; font-weight: bold; color: #475569;">PIS</span>
                                    <input type="number" step="0.01" min="0" name="pis_rate" class="form-control text-right py-1 px-2" style="width: 100px; border: 1px solid #cbd5e1; border-radius: 0.25rem;" placeholder="0,00" value="0.00">
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.85rem; font-weight: bold; color: #475569;">COFINS</span>
                                    <input type="number" step="0.01" min="0" name="cofins_rate" class="form-control text-right py-1 px-2" style="width: 100px; border: 1px solid #cbd5e1; border-radius: 0.25rem;" placeholder="0,00" value="0.00">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn shadow" style="width: 100%; background: #4f46e5; border: none; color: white; padding: 1rem; font-weight: bold; border-radius: 0.5rem; cursor: pointer; font-size: 1rem; transition: transform 0.2s;">
                            <i class="fa fa-save"></i> Gravar Regra de Enquadramento
                        </button>
                    </form>
                </div>
            </div>

            <!-- Painel Direito: Lista de Regras (Motor Vivo) -->
            <div>
                <div class="card shadow-sm border-0 bg-white" style="border-radius: 0.75rem; overflow: hidden; min-height: 500px;">
                    <div style="padding: 1.5rem; background: white; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="font-size: 1rem; font-weight: bold; color: #1e293b; margin: 0;"><i class="fa fa-tasks text-slate-400 mr-2"></i> Regras Ativas no Motor PDV</h3>
                        <p style="font-size: 0.8rem; color: #64748b; margin: 0; margin-top: 0.25rem;">Essas chaves engajam dinamicamente a cada Faturamento / Checkout na loja.</p>
                    </div>

                    <div style="padding: 1rem; overflow-x: auto;">
                        <table class="display responsive nowrap w-100" id="taxes-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.85rem;">
                                    <th style="padding: 1rem; text-align: left;">Cód. / NCM / Regime</th>
                                    <th style="padding: 1rem; text-align: left;">CFOP (Natureza)</th>
                                    <th style="padding: 1rem; text-align: left;">Tributos (ICMS/CSOSN)</th>
                                    <th style="padding: 1rem; text-align: right;">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const initTaxesTable = () => {
                            if (typeof window.AppServerTable !== 'function') {
                                setTimeout(initTaxesTable, 100);
                                return;
                            }
                            new window.AppServerTable('#taxes-table', '/fiscal/configuracoes/tributos/datatable', [
                                { data: 'ncm', name: 'ncm', searchable: true },
                                { data: 'cfop', name: 'cfop', searchable: true },
                                { data: 'icms', searchable: false, orderable: false },
                                { data: 'status', name: 'is_active', searchable: false, className: 'text-right' }
                            ], [[0, 'desc']]); // Fallback order
                        };
                        initTaxesTable();
                    });
                </script>
            </div>

        </div>
    </div>
</x-layouts.app>
