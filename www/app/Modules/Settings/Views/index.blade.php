<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 border-b border-slate-200 pb-4" style="margin-bottom: 2rem;">
            <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Definições Globais do Sistema</h2>
            <p class="text-light" style="margin-top: 0.25rem;">Configurações Base de Loja, Credenciais Fiscais e Equipamentos do PDV.</p>
        </div>

        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <style>
                /* O CSS no app.scss ou inline fará o grid responsivo. Aqui vamos forçar 1/3 para Lg */
                @media (min-width: 1024px) {
                    .settings-grid { grid-template-columns: 300px 1fr !important; }
                }
            </style>
            
            <div class="settings-grid" style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                <!-- Sidebar Nav -->
                <div>
                    <div class="card shadow-sm border-0 bg-white" style="border-radius: 0.75rem; overflow: hidden; position: sticky; top: 1.5rem;">
                        <nav style="display: flex; flex-direction: column;">
                            <button type="button" onclick="switchTab('store')" id="tab-btn-store" class="tab-btn" style="padding: 1.25rem 1.5rem; text-align: left; background: #eef2ff; border: none; border-left: 4px solid #4f46e5; border-bottom: 1px solid #f1f5f9; color: #4338ca; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; transition: background 0.2s;">
                                <i class="fa fa-store" style="font-size: 1.125rem;"></i> Loja e Identidade
                            </button>
                            <button type="button" onclick="switchTab('fiscal')" id="tab-btn-fiscal" class="tab-btn" style="padding: 1.25rem 1.5rem; text-align: left; background: white; border: none; border-left: 4px solid transparent; border-bottom: 1px solid #f1f5f9; color: #475569; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; transition: background 0.2s;">
                                <i class="fa fa-file-invoice-dollar" style="font-size: 1.125rem;"></i> Dados Fiscais (NFC-e)
                            </button>
                            <button type="button" onclick="switchTab('pos')" id="tab-btn-pos" class="tab-btn" style="padding: 1.25rem 1.5rem; text-align: left; background: white; border: none; border-left: 4px solid transparent; border-bottom: 1px solid transparent; color: #475569; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; transition: background 0.2s;">
                                <i class="fa fa-desktop" style="font-size: 1.125rem;"></i> Hardware PDV & Recibos
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Conteúdo -->
                <div>
                    <div class="card shadow-sm border-0 bg-white" style="border-radius: 0.75rem; overflow: hidden; display: flex; flex-direction: column; min-height: 500px;">
                        
                        <!-- TAB: LOJA -->
                        <div id="tab-content-store" class="tab-content" style="display: block; padding: 2rem;">
                            <h3 style="font-size: 1.25rem; font-weight: bold; color: #1e293b; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">Identidade da Filial / Loja</h3>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Nome Fantasia (Apresentação)</label>
                                    <input type="text" name="settings[store_name]" value="{{ $allSettings['store_name'] ?? 'Minha Loja Genérica' }}" required class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                    <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.35rem;">Nome curto exibido no topo do PDV.</p>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Telefone de Contato Principal</label>
                                    <input type="text" name="settings[store_phone]" value="{{ $allSettings['store_phone'] ?? '' }}" placeholder="(XX) 9999-9999" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Endereço Completo</label>
                                <input type="text" name="settings[store_address]" value="{{ $allSettings['store_address'] ?? '' }}" placeholder="Av Principal, N 10 - Centro" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                            </div>
                        </div>

                        <!-- TAB: FISCAL -->
                        <div id="tab-content-fiscal" class="tab-content" style="display: none; padding: 2rem;">
                            <h3 style="font-size: 1.25rem; font-weight: bold; color: #1e293b; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">Ambiente Fiscal & Receita</h3>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Razão Social (Empresa)</label>
                                    <input type="text" name="settings[fiscal_company_name]" value="{{ $allSettings['fiscal_company_name'] ?? '' }}" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">CNPJ</label>
                                    <input type="text" name="settings[fiscal_cnpj]" value="{{ $allSettings['fiscal_cnpj'] ?? '' }}" placeholder="00.000.000/0001-00" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Inscrição Estadual (IE)</label>
                                    <input type="text" name="settings[fiscal_ie]" value="{{ $allSettings['fiscal_ie'] ?? '' }}" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Regime Tributário</label>
                                    <select name="settings[fiscal_regime]" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                        <option value="simples" {{ ($allSettings['fiscal_regime'] ?? '') === 'simples' ? 'selected' : '' }}>Simples Nacional</option>
                                        <option value="normal" {{ ($allSettings['fiscal_regime'] ?? '') === 'normal' ? 'selected' : '' }}>Regime Normal (Lucro Presumido/Real)</option>
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Ambiente de Operação (SEFAZ)</label>
                                <select name="settings[fiscal_environment]" style="width: 100%; padding: 0.75rem; background: #fff1f2; border: 1px solid #fecaca; color: #be123c; font-weight: bold; border-left: 4px solid #e11d48; border-radius: 0.35rem; outline: none;">
                                    <option value="2" {{ ($allSettings['fiscal_environment'] ?? '2') == '2' ? 'selected' : '' }}>AMBIENTE 2: Homologação (Ambiente Seguro de Testes)</option>
                                    <option value="1" {{ ($allSettings['fiscal_environment'] ?? '') == '1' ? 'selected' : '' }}>AMBIENTE 1: Produção (Emissão Legal com Valor Jurídico)</option>
                                </select>
                                <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.35rem;">ATENÇÃO: Mudar para Produção emitirá notas reais na Receita Federal sob este CNPJ e Certificado Digital.</p>
                            </div>

                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1.5rem;">
                                <h4 style="font-size: 1rem; font-weight: bold; color: #1e293b; margin-bottom: 1.5rem;"><i class="fa fa-key" style="color: #6366f1; margin-right: 0.5rem;"></i> Certificado A1 e Tokens</h4>
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Upload de Certificado (.pfx)</label>
                                        <input type="file" name="certificate" accept=".pfx" class="form-control" style="background: white; border: 1px solid #cbd5e1; border-radius: 0.35rem; width: 100%; padding: 0.5rem;">
                                        @if(isset($allSettings['fiscal_certificate_path']) && $allSettings['fiscal_certificate_path'])
                                            <p style="font-size: 0.75rem; color: #059669; margin-top: 0.5rem; font-weight: bold;">Certificado já carregado: ✅ Ativo</p>
                                        @endif
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Senha do Certificado A1</label>
                                        <input type="password" name="settings[fiscal_certificate_password]" value="{{ $allSettings['fiscal_certificate_password'] ?? '' }}" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                    </div>
                                </div>
                                
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Token CSC (Código de Segurança NFC-e)</label>
                                    <input type="text" name="settings[fiscal_csc_token]" value="{{ $allSettings['fiscal_csc_token'] ?? '' }}" class="form-control" style="width: 100%; padding: 0.75rem; font-family: monospace; background: white; border: 1px solid #cbd5e1; border-radius: 0.35rem;">
                                </div>
                            </div>
                        </div>

                        <!-- TAB: PDV -->
                        <div id="tab-content-pos" class="tab-content" style="display: none; padding: 2rem;">
                            <h3 style="font-size: 1.25rem; font-weight: bold; color: #1e293b; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">Terminal PDV e Impressões Minitérmicas</h3>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Largura da Bobina Térmica</label>
                                    <select name="settings[pos_printer_width]" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                        <option value="80mm" {{ ($allSettings['pos_printer_width'] ?? '') === '80mm' ? 'selected' : '' }}>80 mm (Padrão Comercial)</option>
                                        <option value="58mm" {{ ($allSettings['pos_printer_width'] ?? '') === '58mm' ? 'selected' : '' }}>58 mm (Míni Impressoras)</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">IP ou Rota da Impressora de Rede</label>
                                    <input type="text" name="settings[pos_printer_ip]" value="{{ $allSettings['pos_printer_ip'] ?? '' }}" placeholder="Ex: localhost ou 192.168.0.10" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem;">
                                </div>
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #475569; margin-bottom: 0.5rem;">Rodapé Imutável do Cupom (Obrigado pela preferência)</label>
                                <textarea name="settings[pos_receipt_footer]" class="form-control hover-border transition" style="width: 100%; padding: 0.75rem; min-height: 5rem; resize: vertical;" placeholder="Agradecemos sua visita!">{{ $allSettings['pos_receipt_footer'] ?? '' }}</textarea>
                            </div>

                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1.5rem;">
                                <h4 style="font-size: 1rem; font-weight: bold; color: #1e293b; margin-bottom: 1.5rem;"><i class="fa fa-plug" style="color: #6366f1; margin-right: 0.5rem;"></i> Diagnóstico de Hardware ESC/POS</h4>
                                
                                <div style="display: flex; flex-direction: column; gap: 1rem;">
                                    <div>
                                        <button type="button" id="btn-test-printer" onclick="testThermalPrinter()" class="btn shadow border-0" style="background: #4f46e5; color: white; padding: 0.75rem 1.5rem; font-weight: bold; border-radius: 0.5rem; cursor: pointer; transition: background 0.2s;">
                                            <i class="fa fa-print" style="margin-right: 0.5rem;"></i> Testar Guincho e Corte (Salve o IP antes)
                                        </button>
                                    </div>
                                    <div id="printer-result" style="display: none; padding: 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-family: monospace; border: 1px solid #e2e8f0;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Master: Estilos Fixos no Bottom -->
                        <div style="margin-top: auto; padding: 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn shadow" style="background: #4f46e5; border: none; color: white; padding: 0.75rem 2rem; font-weight: bold; border-radius: 0.5rem; cursor: pointer; font-size: 1rem; transition: transform 0.2s;">
                                Salvar Alterações (Mutações Globais)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Script Vanilla Substituindo o AlpineJS x-data/x-show -->
    <script>
        function switchTab(tabId) {
            // Esconder todos os conteúdos
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(function(content) {
                content.style.display = 'none';
            });
            
            // Redefinir as cores de todos os botões (Inativos)
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(function(btn) {
                btn.style.background = 'white';
                btn.style.color = '#475569';
                btn.style.fontWeight = '500';
                btn.style.borderLeftColor = 'transparent';
            });

            // Mostrar a aba selecionada
            document.getElementById('tab-content-' + tabId).style.display = 'block';

            // Estilizar o Botão Ativo
            const activeBtn = document.getElementById('tab-btn-' + tabId);
            activeBtn.style.background = '#eef2ff';
            activeBtn.style.color = '#4338ca';
            activeBtn.style.fontWeight = 'bold';
            activeBtn.style.borderLeftColor = '#4f46e5';
        }

        // Script da Impressora ESC/POS
        function testThermalPrinter() {
            const btn = document.getElementById('btn-test-printer');
            const resultBox = document.getElementById('printer-result');

            btn.innerHTML = '<i class="fa fa-spinner fa-spin" style="margin-right: 0.5rem;"></i> Disparando Impressão TCP/IP...';
            btn.disabled = true;
            btn.style.opacity = '0.7';
            
            resultBox.style.display = 'none';
            resultBox.innerHTML = '';

            fetch('{{ route("settings.printer.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = '<i class="fa fa-print" style="margin-right: 0.5rem;"></i> Testar Guincho e Corte (Salve o IP antes)';
                btn.disabled = false;
                btn.style.opacity = '1';
                resultBox.style.display = 'block';

                if (data.success) {
                    resultBox.style.background = '#ecfdf5';
                    resultBox.style.borderColor = '#a7f3d0';
                    resultBox.style.color = '#047857';
                    resultBox.innerHTML = `<strong>SUCESSO TÉRMICO:</strong><br>${data.message}`;
                } else {
                    resultBox.style.background = '#fef2f2';
                    resultBox.style.borderColor = '#fecaca';
                    resultBox.style.color = '#b91c1c';
                    resultBox.innerHTML = `<strong>FALHA NA CONEXÃO:</strong><br>${data.error}`;
                }
            })
            .catch(err => {
                btn.innerHTML = '<i class="fa fa-print" style="margin-right: 0.5rem;"></i> Testar Guincho e Corte (Salve o IP antes)';
                btn.disabled = false;
                btn.style.opacity = '1';
                
                resultBox.style.display = 'block';
                resultBox.style.background = '#fef2f2';
                resultBox.style.borderColor = '#fecaca';
                resultBox.style.color = '#b91c1c';
                resultBox.innerHTML = `<strong>ERRO FATAL DE SERVIDOR:</strong> Ocorreu um erro no PHP ou HTTP.`;
            });
        }
        
        // Estilos hover genéricos e botões
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                if(btn.style.borderLeftColor !== 'rgb(79, 70, 229)') {
                    btn.style.background = '#f8fafc';
                }
            });
            btn.addEventListener('mouseleave', () => {
                if(btn.style.borderLeftColor !== 'rgb(79, 70, 229)') {
                    btn.style.background = 'white';
                }
            });
        });
    </script>
</x-layouts.app>