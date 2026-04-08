<x-layouts.app>
<div class="p-6">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-2xl fw-bold text-slate-800">Definições Globais do Sistema</h2>
            <p class="text-slate-500">Configurações Base de Loja, Credenciais Fiscais e PDV.</p>
        </div>
    </div>

    <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 relative" x-data="{ tab: 'store' }">
            
            <!-- Sidebar de Navegação de Configurações -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
                    <nav class="flex flex-col">
                        <button type="button" @click="tab = 'store'" :class="{'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 font-bold': tab === 'store', 'text-slate-600 hover:bg-slate-50 border-l-4 border-transparent': tab !== 'store'}" class="p-4 text-left transition-colors font-medium border-b border-slate-100 flex items-center gap-3">
                            <i class="fa fa-store text-lg"></i> Loja e Identidade
                        </button>
                        <button type="button" @click="tab = 'fiscal'" :class="{'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 font-bold': tab === 'fiscal', 'text-slate-600 hover:bg-slate-50 border-l-4 border-transparent': tab !== 'fiscal'}" class="p-4 text-left transition-colors font-medium border-b border-slate-100 flex items-center gap-3">
                            <i class="fa fa-file-invoice-dollar text-lg"></i> Dados Fiscais (NFC-e)
                        </button>
                        <button type="button" @click="tab = 'pos'" :class="{'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700 font-bold': tab === 'pos', 'text-slate-600 hover:bg-slate-50 border-l-4 border-transparent': tab !== 'pos'}" class="p-4 text-left transition-colors font-medium flex items-center gap-3">
                            <i class="fa fa-desktop text-lg"></i> Hardware PDV & Recibos
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Conteúdo Principal das Configurações -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                    
                    <!-- Aba: Loja -->
                    <div x-show="tab === 'store'" class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Identidade da Filial / Loja</h3>
                        
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nome Fantasia (Apresentação)</label>
                                <input type="text" name="settings[store_name]" value="{{ $allSettings['store_name'] ?? 'Minha Loja Genérica' }}" class="form-control w-full bg-slate-50 focus:bg-white">
                                <p class="text-xs text-slate-400 mt-1">Nome curto exibido no topo do PDV.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Telefone de Contato Principal</label>
                                <input type="text" name="settings[store_phone]" value="{{ $allSettings['store_phone'] ?? '' }}" placeholder="(XX) 9999-9999" class="form-control w-full bg-slate-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Endereço Completo</label>
                            <input type="text" name="settings[store_address]" value="{{ $allSettings['store_address'] ?? '' }}" placeholder="Av Principal, N 10 - Centro" class="form-control w-full bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    <!-- Aba: Fiscal -->
                    <div x-show="tab === 'fiscal'" class="p-6" style="display: none;">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Ambiente Fiscal & Receita</h3>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Razão Social (Empresa)</label>
                                <input type="text" name="settings[fiscal_company_name]" value="{{ $allSettings['fiscal_company_name'] ?? '' }}" class="form-control w-full bg-slate-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">CNPJ</label>
                                <input type="text" name="settings[fiscal_cnpj]" value="{{ $allSettings['fiscal_cnpj'] ?? '' }}" placeholder="00.000.000/0001-00" class="form-control w-full bg-slate-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Inscrição Estadual (IE)</label>
                                <input type="text" name="settings[fiscal_ie]" value="{{ $allSettings['fiscal_ie'] ?? '' }}" class="form-control w-full bg-slate-50 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Regime Tributário</label>
                                <select name="settings[fiscal_regime]" class="form-control w-full bg-slate-50 focus:bg-white">
                                    <option value="simples" {{ ($allSettings['fiscal_regime'] ?? '') === 'simples' ? 'selected' : '' }}>Simples Nacional</option>
                                    <option value="normal" {{ ($allSettings['fiscal_regime'] ?? '') === 'normal' ? 'selected' : '' }}>Regime Normal (Lucro Presumido/Real)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Ambiente de Operação (SEFAZ)</label>
                            <select name="settings[fiscal_environment]" class="form-control w-full bg-slate-50 focus:bg-white font-bold border-l-4 border-rose-500 text-rose-700">
                                <option value="2" {{ ($allSettings['fiscal_environment'] ?? '2') == '2' ? 'selected' : '' }}>AMBIENTE 2: Homologação (Ambiente Seguro de Testes)</option>
                                <option value="1" {{ ($allSettings['fiscal_environment'] ?? '') == '1' ? 'selected' : '' }}>AMBIENTE 1: Produção (Emissão Legal com Valor Jurídico)</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">ATENÇÃO: Mudar para Produção emitirá notas reais na Receita Federal sob este CNPJ e Certificado Digital.</p>
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg mb-6 shadow-sm">
                            <h4 class="font-bold text-slate-800 mb-4"><i class="fa fa-key text-indigo-500"></i> Certificado A1 e Tokens</h4>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Upload de Certificado (.pfx)</label>
                                    <input type="file" name="certificate" accept=".pfx" class="form-control w-full p-2 bg-white text-sm">
                                    @if(isset($allSettings['fiscal_certificate_path']) && $allSettings['fiscal_certificate_path'])
                                        <p class="text-xs text-emerald-600 mt-2 font-semibold">Certificado já carregado: ✅ Ativo</p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Senha do Certificado A1</label>
                                    <input type="password" name="settings[fiscal_certificate_password]" value="{{ $allSettings['fiscal_certificate_password'] ?? '' }}" class="form-control w-full bg-slate-50 focus:bg-white">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Token CSC (Código de Segurança NFC-e)</label>
                                <input type="text" name="settings[fiscal_csc_token]" value="{{ $allSettings['fiscal_csc_token'] ?? '' }}" class="form-control w-full font-mono bg-slate-50">
                            </div>
                        </div>
                    </div>

                    <!-- Aba: PDV & Recibos -->
                    <div x-show="tab === 'pos'" class="p-6" style="display: none;">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Terminal PDV e Impressões Minitérmicas</h3>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Largura da Bobina Térmica</label>
                                <select name="settings[pos_printer_width]" class="form-control w-full bg-slate-50 focus:bg-white">
                                    <option value="80mm" {{ ($allSettings['pos_printer_width'] ?? '') === '80mm' ? 'selected' : '' }}>80 mm (Padrão e Restaurantes)</option>
                                    <option value="58mm" {{ ($allSettings['pos_printer_width'] ?? '') === '58mm' ? 'selected' : '' }}>58 mm (Míni Impressoras)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">IP ou Rota da Impressora de Rede</label>
                                <input type="text" name="settings[pos_printer_ip]" value="{{ $allSettings['pos_printer_ip'] ?? '' }}" placeholder="Ex: localhost ou 192.168.0.10" class="form-control w-full bg-slate-50 focus:bg-white">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Rodapé Imutável do Cupom (Obrigado pela preferência)</label>
                            <textarea name="settings[pos_receipt_footer]" class="form-control w-full h-24 bg-slate-50 focus:bg-white" placeholder="Agradecemos sua visita! Deus é fiel.">{{ $allSettings['pos_receipt_footer'] ?? '' }}</textarea>
                        </div>
                        
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg mb-6 shadow-sm">
                            <h4 class="font-bold text-slate-800 mb-4"><i class="fa fa-plug text-indigo-500"></i> Diagnóstico de Hardware ESC/POS</h4>
                            <div class="flex flex-col gap-4">
                                <div class="flex gap-2">
                                    <button type="button" id="btn-test-printer" onclick="testThermalPrinter()" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded">
                                        <i class="fa fa-print"></i> Testar Guincho e Corte (Requer salvar o IP primeiro)
                                    </button>
                                </div>
                                <div id="printer-result" class="hidden p-4 rounded-lg text-sm font-mono border">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Salvar -->
                    <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition-transform transform hover:-translate-y-1">
                            Salvar Alterações (Mutações Globais)
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
    <!-- Incluir Alpine JS caso o master n tenha -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <script>
        function testThermalPrinter() {
            const btn = document.getElementById('btn-test-printer');
            const resultBox = document.getElementById('printer-result');
            
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Disparando Impressão TCP/IP...';
            btn.disabled = true;
            resultBox.classList.add('hidden');
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
                btn.innerHTML = '<i class="fa fa-print"></i> Testar Guincho e Corte (Requer salvar o IP primeiro)';
                btn.disabled = false;
                resultBox.classList.remove('hidden');

                if(data.success) {
                    resultBox.className = 'p-4 rounded-lg text-sm font-mono border bg-emerald-50 border-emerald-200 text-emerald-700';
                    resultBox.innerHTML = `<strong>SUCESSO TÉRMICO:</strong><br>${data.message}`;
                } else {
                    resultBox.className = 'p-4 rounded-lg text-sm font-mono border bg-red-50 border-red-200 text-red-700';
                    resultBox.innerHTML = `<strong>FALHA NA CONEXÃO:</strong><br>${data.error}`;
                }
            })
            .catch(err => {
                btn.innerHTML = '<i class="fa fa-print"></i> Testar Guincho e Corte (Requer salvar o IP primeiro)';
                btn.disabled = false;
                resultBox.classList.remove('hidden');
                resultBox.className = 'p-4 rounded-lg text-sm font-mono border bg-red-50 border-red-200 text-red-700';
                resultBox.innerHTML = `<strong>ERRO FATAL DE SERVIDOR:</strong> Ocorreu um erro no PHP ou HTTP.`;
            });
        }
    </script>
</x-layouts.app>
