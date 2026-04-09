@php
    $isTerminal = request()->is('terminal*');
    $prefixPath = $isTerminal ? '/terminal' : '/vendas/pdv';
@endphp
<x-layouts.pos>
    <!-- Área de Produtos e Leitor -->
    <div class="pos-terminal" style="user-select: none; background-color: #0f172a;">
        <div class="pos-search-area border-b border-white/10 pb-4" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
            <div class="relative" style="position: relative;">
                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.25rem;">🛒</span>
                <input type="text" id="barcode-input" placeholder="Bipador de Código de Barras (Mantenha o Foco Aqui)..." autocomplete="off" autofocus style="width: 100%; padding: 1rem 1rem 1rem 3rem; background: rgba(30,41,59,0.8); border: 1px solid #334155; border-radius: 12px; color: white; font-size: 1.1rem; outline: none; box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);">
            </div>
        </div>

        <div style="margin-bottom: 1.5rem; margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.15rem; color: #cbd5e1; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Ações e Catálogo Rápidos</h2>
            
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn relative overflow-hidden group" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.85rem; backdrop-filter: blur(10px);" onclick="window.PosApp.cashMovement('SANGRIA')">
                    <span class="relative z-10">🩸 SANGRIA</span>
                    <div class="absolute inset-0 bg-red-500/20 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                </button>
                <button type="button" class="btn relative overflow-hidden group" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.85rem; backdrop-filter: blur(10px);" onclick="window.PosApp.cashMovement('REFORCO')">
                    <span class="relative z-10">🏦 REFORÇO</span>
                    <div class="absolute inset-0 bg-emerald-500/20 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                </button>
                <a href="{{ $prefixPath }}/fechar" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fcd34d; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.85rem; text-decoration: none; transition: 0.2s; display: inline-block; cursor: pointer; backdrop-filter: blur(10px);" onmouseover="this.style.background='rgba(245, 158, 11, 0.3)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.2)'">
                    <span>🔒 FECHAR CAIXA</span>
                </a>
            </div>
        </div>

        <!-- Elegante Modal de Movimentação de Dinheiro -->
        <dialog id="movement-modal" style="padding: 0; border: none; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); width: 400px; max-width: 90vw;">
            <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display:flex; justify-content: space-between; align-items:center;">
                <h3 id="movement-modal-title" style="margin:0; font-size: 1.2rem; color: #0f172a; font-weight:700;">Nova Movimentação</h3>
                <button type="button" onclick="document.getElementById('movement-modal').close()" style="background:transparent; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
            </div>
            <form id="cash-movement-form" method="POST" action="{{ $prefixPath }}/movimento" style="padding: 1.5rem;">
                @csrf
                <input type="hidden" name="type" id="movement-type">
                
                <div style="margin-bottom: 1rem;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">Valor da Operação (R$)</label>
                    <input type="text" name="amount" id="movement-amount" placeholder="0,00" required style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:1.1rem; text-align:center;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">Motivo / Destino</label>
                    <input type="text" name="reason" id="movement-reason" placeholder="Ex: Pagamento de Fornecedor..." required style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">PIN Seguridade (Supervisor/Operador Responsável)</label>
                    <input type="password" name="supervisor_pin" id="movement-pin" placeholder="****" required style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; text-align:center; letter-spacing:0.2rem;">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding: 1rem; font-weight:bold; font-size:1rem; background: #0f172a;">AUTORIZAR RETIRADA/INJEÇÃO</button>
            </form>
        </dialog>

        <div class="pos-product-grid">
            <!-- Mock visual que injetará direto no Carrinho Javascript via Dataset ID -->
            @forelse($products as $product)
                <div class="pos-product-card catalog-item glass-card" 
                     data-id="{{ $product->id }}" 
                     data-name="{{ $product->name }}" 
                     data-price="{{ $product->sale_price->getCents() }}"
                     data-club-price="{{ $product->club_price ? $product->club_price->getCents() : '' }}"
                     data-barcode="{{ $product->barcode ?? str_pad($product->id, 8, '0', STR_PAD_LEFT) }}">
                     
                    <h3 style="color: #e2e8f0; font-weight: 500;">{{ \Illuminate\Support\Str::limit($product->name, 25) }}</h3>
                    <div class="price" style="color: #38bdf8; text-shadow: 0 0 10px rgba(56, 189, 248, 0.4);">{{ clone $product->sale_price }}</div>
                    <span style="font-size: 0.75rem; color: #64748b; margin-top:0.5rem; background: rgba(0,0,0,0.3); padding: 0.2rem 0.5rem; border-radius: 4px;">Estoque: {{ $product->current_stock }}</span>
                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; color: #64748b; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
                    <div style="font-size: 2rem; margin-bottom: 1rem;">📦</div>
                    Nenhum produto com saldo em estoque para venda rápida.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Carrinho Analítico (Cupom Fiscal) -->
    <aside class="pos-cart" style="background: #0f172a; border-left: 1px solid rgba(255,255,255,0.1); display: flex; flex-direction: column;">
        <div style="padding: 1.25rem; background: linear-gradient(180deg, rgba(30,41,59,1) 0%, rgba(15,23,42,1) 100%); border-bottom: 1px solid rgba(255,255,255,0.05); text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3);">
            <strong style="color: #38bdf8; font-family: monospace; font-size: 1.1rem; letter-spacing: 0.1em; text-transform: uppercase;">CUPOM FISCAL LIVRE</strong>
        </div>

        <!-- Renderizado pelo TypeScript PosManager -->
        <div class="pos-cart-items" id="cart-items-container" style="flex: 1; overflow-y: auto; padding: 0.5rem;">
            <!-- Vazio por Padrão -->
        </div>

        <!-- Bloco de Identificação/CRM -->
        <div style="background: rgba(15,23,42,0.95); padding: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <label style="color: #94a3b8; font-size: 0.8rem; text-transform: uppercase;">CPF/CNPJ na Nota (CRM)</label>
                <span id="crm-status" style="font-size: 0.75rem; color: #cbd5e1; background: rgba(255,255,255,0.1); padding: 0.1rem 0.4rem; border-radius: 4px;">Consumidor Padrão</span>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" id="customer-doc-input" placeholder="Digite apenas números..." style="width: 100%; padding:0.5rem; border:1px solid #334155; border-radius:6px; font-size:1rem; background:#1e293b; color:white; outline:none;" autocomplete="off">
                <button type="button" class="btn" style="background:#3b82f6; color:white; border:none; border-radius:6px; padding: 0 1rem; cursor:pointer;" onclick="PosApp.verifyCustomer()"><i class="fa fa-search"></i>🔍</button>
            </div>
            <small id="crm-helper" style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 0.5rem;">Aperte Enter para buscar. Cliente Clube ganha desconto automático.</small>
        </div>

        <div class="pos-cart-summary" style="background: rgba(15,23,42,0.95); backdrop-filter: blur(10px); border-top: 1px solid rgba(255,255,255,0.1); padding: 1.5rem;">
            <div class="summary-row" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: baseline;">
                <span style="color: #94a3b8; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">SUBTOTAL</span>
                <strong id="cart-subtotal" style="font-size: 2.5rem; color: #10b981; font-weight: 800; font-family: monospace; text-shadow: 0 0 20px rgba(16, 185, 129, 0.3);">R$ 0,00</strong>
            </div>

            <!-- Botões Acionam o Checkout via Typescript que injeta num formulário Ajax -->
            <div class="pos-actions" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <button type="button" class="btn-pos glass-btn" data-method="Dinheiro" style="grid-column: span 2; background: linear-gradient(135deg, rgba(16,185,129,0.2) 0%, rgba(5,150,105,0.4) 100%); border-color: rgba(16,185,129,0.5); color: #a7f3d0; height: 3.5rem;">
                    <div style="display:flex; justify-content:space-between; width:100%; align-items:center; padding: 0 1rem;">
                        <span><kbd style="background:rgba(0,0,0,0.3); border-radius:4px; padding:0.2rem 0.4rem; font-size:0.75rem; margin-right:0.5rem;">F2</kbd>DINHEIRO FÍSICO</span>
                        <span style="font-size:1.5rem;">💵</span>
                    </div>
                </button>
                <button type="button" class="btn-pos glass-btn" data-method="Credito" style="background: linear-gradient(135deg, rgba(59,130,246,0.2) 0%, rgba(37,99,235,0.4) 100%); border-color: rgba(59,130,246,0.5); color: #bfdbfe;">
                    <div style="display:flex; align-items:center; justify-content:center; gap: 0.5rem;">
                        <span>CRÉDITO</span><span style="font-size: 1.2rem;">💳</span>
                    </div>
                </button>
                <button type="button" class="btn-pos glass-btn" data-method="Debito" style="background: linear-gradient(135deg, rgba(14,165,233,0.2) 0%, rgba(2,132,199,0.4) 100%); border-color: rgba(14,165,233,0.5); color: #bae6fd;">
                    <div style="display:flex; align-items:center; justify-content:center; gap: 0.5rem;">
                        <kbd style="background:rgba(0,0,0,0.3); border-radius:4px; padding:0.2rem 0.4rem; font-size:0.75rem; position:absolute; left: 0.5rem;">F4</kbd>
                        <span>DÉBITO</span><span style="font-size: 1.2rem;">💳</span>
                    </div>
                </button>
                <button type="button" class="btn-pos glass-btn" data-method="Pix" style="grid-column: span 2; background: linear-gradient(135deg, rgba(168,85,247,0.2) 0%, rgba(147,51,234,0.4) 100%); border-color: rgba(168,85,247,0.5); color: #e9d5ff;">
                    <div style="display:flex; justify-content:center; align-items:center; gap: 0.5rem;">
                        <span>RECEBER VIA PIX</span><span style="font-size: 1.2rem;">💠</span>
                    </div>
                </button>
            </div>
            
            <!-- Hidden Form to BackEnd PointOfSaleController -->
            <form id="checkout-form" method="POST" action="{{ $prefixPath }}/checkout" style="display:none;">
                @csrf
                <input type="hidden" name="payload_json" id="checkout-payload" value="">
            </form>
        </div>
    </aside>

    <!-- Loader Fullscreen (Atraso Sefaz) -->
    <div id="sefaz-loader" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.9); z-index: 9999; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
        <div style="font-size: 4rem; color: #38bdf8; animation: pulse 1s infinite alternate;">🧾</div>
        <h2 style="color: white; margin-top: 1rem; font-size: 1.5rem; font-weight: bold; letter-spacing: 0.05em;">PROCESSANDO PAGAMENTO...</h2>
        <p style="color: #94a3b8; font-size: 1rem; margin-top: 0.5rem;" id="sefaz-loader-text">Emitindo NFC-e e acionando Minitérmica (SEFAZ)...</p>
    </div>

    @if(session('sale_id'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const url = "{{ $prefixPath }}/cupom/" + {{ session('sale_id') }};
                const popup = window.open(url, 'ImpressaoCupom', 'width=380,height=600,scrollbars=yes,resizable=no');
                if(popup) popup.focus();
            });
        </script>
    @endif

    <!-- Modal de Cadastro de Cliente (CRM) -->
    <dialog id="crm-register-modal" style="padding: 0; border: none; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); width: 450px; max-width: 90vw;">
        <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display:flex; justify-content: space-between; align-items:center;">
            <h3 style="margin:0; font-size: 1.2rem; color: #0f172a; font-weight:700;">Cadastrar no Clube 💎</h3>
            <button type="button" onclick="document.getElementById('crm-register-modal').close()" style="background:transparent; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
        </div>
        <form id="crm-register-form" onsubmit="event.preventDefault(); window.PosApp.submitCustomerRegistration();" style="padding: 1.5rem;">
            <div style="margin-bottom: 1rem;">
                <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">Documento (CPF/CNPJ)</label>
                <input type="text" id="crm-reg-doc" required readonly style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; background:#f1f5f9; color:#64748b;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">Nome Completo</label>
                <input type="text" id="crm-reg-name" required placeholder="João da Silva" style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">Celular / WhatsApp</label>
                    <input type="text" id="crm-reg-phone" placeholder="(11) 9..." style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px;">
                </div>
                <div>
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">E-mail</label>
                    <input type="email" id="crm-reg-email" placeholder="email@ext.com" style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px;">
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display:block; font-size: 0.85rem; font-weight:600; color:#475569; margin-bottom:0.25rem;">Endereço de Faturamento</label>
                <input type="text" id="crm-reg-address" placeholder="Rua..." style="width: 100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px;">
            </div>
            
            <div style="margin-bottom: 1.5rem; display:flex; align-items:flex-start; gap:0.5rem; background:rgba(59,130,246,0.1); padding:1rem; border-radius:8px;">
                <input type="checkbox" id="crm-reg-lgpd" required style="margin-top:0.25rem;">
                <label for="crm-reg-lgpd" style="font-size:0.8rem; color:#1e293b; line-height:1.4;">Declaro sob as diretrizes vigentes da LGPD ter obtido o consentimento expresso e presencial do titular para o armazenamento destes dados gerenciais para participação no Clube de Vantagens.</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding: 1rem; font-weight:bold; font-size:1rem; background: #2563eb;">CADASTRAR E ATIVAR DESCONTOS</button>
        </form>
    </dialog>

    <!-- Modal de Revisão de Compra e Troco -->
    <dialog id="checkout-review-modal" style="padding: 0; border: none; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 450px; max-width: 90vw;">
        <div style="padding: 1.5rem; background: #0f172a; border-bottom: 1px solid rgba(255,255,255,0.1); display:flex; justify-content: space-between; align-items:center;">
            <h3 style="margin:0; font-size: 1.25rem; color: #f8fafc; font-weight:700;">Revisão de Pagamento 🧾</h3>
            <button type="button" onclick="document.getElementById('checkout-review-modal').close()" style="background:transparent; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
        </div>
        <div style="padding: 1.5rem; background:#f8fafc;">
            <div style="display:flex; justify-content:space-between; margin-bottom: 1rem; border-bottom: 1px dashed #cbd5e1; padding-bottom: 1rem;">
                <span style="color:#64748b; font-weight:600; font-size:1.1rem;">Meio Físico: <span id="review-method" style="color:#2563eb;"></span></span>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom: 1.5rem;">
                <span style="color:#475569; font-weight:700; font-size:1.2rem;">Total da Compra</span>
                <span id="review-total" style="font-size:2rem; font-weight:800; color:#0f172a;">R$ 0,00</span>
            </div>

            <div id="review-cash-section" style="display:none; background: #e0f2fe; border: 1px solid #bae6fd; padding:1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <label style="display:block; font-size: 0.9rem; font-weight:700; color:#0369a1; margin-bottom:0.5rem;">Valor Entregue pelo Cliente (R$)</label>
                <input type="number" step="0.01" id="review-received" style="width: 100%; padding: 1rem; border:2px solid #38bdf8; border-radius:8px; font-size:1.5rem; font-weight:bold; color:#0f172a; outline:none; text-align:right;">
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 1rem;">
                    <span style="color:#0284c7; font-weight:700; font-size:1.1rem;">Troco</span>
                    <span id="review-change" style="font-size:1.5rem; font-weight:900; color:#059669;">R$ 0,00</span>
                </div>
            </div>

            <button type="button" class="btn" style="width:100%; padding: 1.25rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color:white; font-size:1.2rem; font-weight:800; border:none; border-radius:8px; cursor:pointer; box-shadow:0 4px 6px -1px rgba(16,185,129,0.3);" onclick="PosApp.confirmCheckoutFinal()">
                Efetivar & Imprimir ✅
            </button>
        </div>
    </dialog>

    <!-- Rota do prefixPath nativo p javascript dinâmico -->
    <script>window.POS_PREFIX_PATH = "{{ $prefixPath }}";</script>

    <script>
        // Core POS Javascript Vanilla (No Bundler Required)
        const formatMoney = (cents) => {
            return 'R$ ' + (cents / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const PosApp = {
            cart: [],
            customerIsClub: false,
            customerDocumentChecked: null,
            
            init() {
                this.barcodeInput = document.getElementById('barcode-input');
                this.cartContainer = document.getElementById('cart-items-container');
                this.subtotalEl = document.getElementById('cart-subtotal');
                this.form = document.getElementById('checkout-form');
                this.payloadField = document.getElementById('checkout-payload');
                
                this.docInput = document.getElementById('customer-doc-input');
                this.crmStatus = document.getElementById('crm-status');
                
                // Keep Focus Loop
                document.addEventListener('click', (e) => {
                    if(!e.target.closest('button') && !e.target.closest('a') && e.target.tagName !== 'INPUT' && e.target.tagName !== 'DIALOG') {
                        this.barcodeInput.focus();
                    }
                });

                // Barcode Listener
                this.barcodeInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.processBarcode(this.barcodeInput.value.trim());
                        this.barcodeInput.value = '';
                    }
                });

                // Document Listener
                this.docInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.verifyCustomer();
                    }
                });

                // Catalog Clicks Listener
                document.querySelectorAll('.catalog-item').forEach(card => {
                    card.addEventListener('click', () => {
                        this.addToCart(
                            parseInt(card.dataset.id),
                            card.dataset.name,
                            parseInt(card.dataset.price),
                            card.dataset.clubPrice ? parseInt(card.dataset.clubPrice) : null
                        );
                    });
                });

                // Hotkey Hooks
                window.addEventListener('keydown', (e) => {
                    // F2 -> Dinheiro
                    if (e.key === 'F2') { e.preventDefault(); this.checkout('Dinheiro'); }
                    // F4 -> Debito
                    if (e.key === 'F4') { e.preventDefault(); this.checkout('Debito'); }
                    // ESC -> Limpar Carrinho
                    if (e.key === 'Escape') { e.preventDefault(); this.clearCart(); }
                });

                // Button Listeners
                document.querySelectorAll('.btn-pos').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const method = btn.dataset.method;
                        if(method) this.checkout(method);
                    });
                });

                this.render();
            },

            processBarcode(code) {
                if(!code) return;

                let searchCode = code.trim();
                let quantityToAdd = 1;

                // Captura regex perfeita para o formato: "5*12345" ou "10*SKU99"
                const match = searchCode.match(/^(\d+)\s*\*\s*(.+)$/);
                if (match) {
                    quantityToAdd = parseInt(match[1], 10);
                    searchCode = match[2];
                }

                // Match dynamically
                const card = document.querySelector(`.catalog-item[data-barcode="${searchCode}"]`) || 
                             document.querySelector(`.catalog-item[data-id="${searchCode}"]`);
                
                if (card) {
                    this.addToCart(
                        parseInt(card.dataset.id), 
                        card.dataset.name, 
                        parseInt(card.dataset.price),
                        card.dataset.clubPrice ? parseInt(card.dataset.clubPrice) : null,
                        quantityToAdd
                    );
                } else {
                    if (window.toast) window.toast.fire({ icon: 'error', title: 'Produto/Código não encontrado!' });
                    else alert(`Falha: ${searchCode} não foi localizado no Catálogo Interno.`);
                }
            },

            addToCart(id, name, priceCents, clubPriceCents, qtyToAdd = 1) {
                const existing = this.cart.find(i => i.id === id);
                if (existing) {
                    existing.quantity += qtyToAdd;
                } else {
                    this.cart.unshift({ id, name, originalPriceCents: priceCents, clubPriceCents, quantity: qtyToAdd }); // unshift to put on top of bill
                }
                this.render();
            },

            clearCart() {
                if(this.cart.length > 0) {
                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Cancelar cupom atual?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#475569',
                            confirmButtonText: 'Sim, Limpar',
                            cancelButtonText: 'Manter'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.cart = [];
                                this.render();
                                window.toast.fire({ icon: 'info', title: 'Cupom limpo.' });
                            }
                        });
                    } else {
                        if(confirm('Cancelar cupom atual?')) {
                            this.cart = [];
                            this.render();
                        }
                    }
                }
            },

            checkout(method) {
                if (this.cart.length === 0) {
                    if (window.toast) window.toast.fire({ icon: 'warning', title: 'Carrinho vazio!' });
                    else alert('Carrinho vazio!');
                    return;
                }
                
                // Em vez de enviar o formulário passivamente, abortamos para exibir o Modal de Confirmação visual
                const totalCents = this.cart.reduce((sum, item) => {
                    let price = item.originalPriceCents;
                    if (this.customerIsClub && item.clubPriceCents) price = item.clubPriceCents;
                    return sum + (price * item.quantity);
                }, 0);

                // Armazenar qual foi o meio clicado
                this.tempCheckoutMethod = method;
                
                // Exibir infos no modal construído no HTML abaixo
                document.getElementById('review-method').innerText = method.toUpperCase();
                document.getElementById('review-total').innerText = formatMoney(totalCents);
                
                const receivedInput = document.getElementById('review-received');
                const changeDisplay = document.getElementById('review-change');
                
                if (method.toUpperCase() === 'DINHEIRO') {
                    document.getElementById('review-cash-section').style.display = 'block';
                    receivedInput.value = (totalCents / 100).toFixed(2);
                    changeDisplay.innerText = formatMoney(0);
                    
                    receivedInput.onkeyup = (e) => {
                        let parsed = parseFloat(e.target.value.replace(',','.'));
                        if(isNaN(parsed)) parsed = 0;
                        let rcvCents = Math.round(parsed * 100);
                        let change = rcvCents - totalCents;
                        if (change < 0) change = 0;
                        changeDisplay.innerText = formatMoney(change);
                    };
                    
                } else {
                    document.getElementById('review-cash-section').style.display = 'none';
                }

                document.getElementById('checkout-review-modal').showModal();
            },
            
            confirmCheckoutFinal() {
                document.getElementById('checkout-review-modal').close();

                let finalDoc = this.docInput.value.replace(/\D/g, '');
                
                this.payloadField.value = JSON.stringify({
                    payment_method: this.tempCheckoutMethod,
                    customer_document: finalDoc,
                    items: this.cart.map(i => ({ id: i.id, quantity: i.quantity }))
                });
                
                // Overlay Loader Sefaz
                document.getElementById('sefaz-loader').style.display = 'flex';
                
                this.form.submit();
            },

            async verifyCustomer() {
                const docRaw = this.docInput.value;
                const docPattern = docRaw.replace(/\D/g, '');
                if (docPattern.length < 11) return;

                document.getElementById('crm-helper').innerText = 'Consultando...';
                
                try {
                    const csrf = document.querySelector('input[name="_token"]').value;
                    const res = await fetch(window.POS_PREFIX_PATH + '/check-customer', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf},
                        body: JSON.stringify({ document: docPattern })
                    });
                    const data = await res.json();
                    
                    if(data.found) {
                        this.customerIsClub = data.is_club;
                        this.crmStatus.innerText = data.name + (data.is_club ? ' (VIP)' : '');
                        this.crmStatus.style.background = data.is_club ? 'linear-gradient(90deg, #10b981, #059669)' : '#475569';
                        this.crmStatus.style.color = '#fff';
                        document.getElementById('crm-helper').innerText = 'Identificado. Descontos aplicados automaticamente!';
                        this.render(); // Recalcula total com array
                    } else {
                        if (window.Swal) {
                            window.Swal.fire({
                                title: 'CPF não cadastrado!',
                                text: 'Deseja registrar este cliente agora no Clube de Vantagens?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#3b82f6',
                                cancelButtonColor: '#475569',
                                confirmButtonText: 'Sim, Registrar',
                                cancelButtonText: 'Não, Apenas CPF na Nota'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('crm-reg-doc').value = docPattern;
                                    document.getElementById('crm-register-modal').showModal();
                                } else {
                                    document.getElementById('crm-helper').innerText = 'CPF atrelado como Consumidor Não-Registrado.';
                                }
                            });
                        } else {
                            if(confirm('CPF não cadastrado! Deseja registrar este cliente agora no Clube de Vantagens?')) {
                                document.getElementById('crm-reg-doc').value = docPattern;
                                document.getElementById('crm-register-modal').showModal();
                            } else {
                                document.getElementById('crm-helper').innerText = 'CPF atrelado como Consumidor Não-Registrado.';
                            }
                        }
                    }
                } catch(e) {
                    console.error(e);
                    if (window.toast) window.toast.fire({ icon: 'error', title: 'Falha na comunicação com o CRM.' });
                    document.getElementById('crm-helper').innerText = 'Falha na verificação de CRM.';
                }
            },

            async submitCustomerRegistration() {
                const payload = {
                    document: document.getElementById('crm-reg-doc').value,
                    name: document.getElementById('crm-reg-name').value,
                    phone: document.getElementById('crm-reg-phone').value,
                    email: document.getElementById('crm-reg-email').value,
                    address: document.getElementById('crm-reg-address').value,
                    lgpd: document.getElementById('crm-reg-lgpd').checked
                };

                const csrf = document.querySelector('input[name="_token"]').value;
                const res = await fetch(window.POS_PREFIX_PATH + '/register-customer', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf},
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (data.success) {
                    if (window.toast) window.toast.fire({ icon: 'success', title: 'Cadastrado com sucesso no Clube!' });
                    else alert('Cadastrado com sucesso no Clube!');
                    
                    document.getElementById('crm-register-modal').close();
                    this.docInput.value = payload.document;
                    this.verifyCustomer();
                } else {
                    if (window.toast) window.toast.fire({ icon: 'error', title: data.message || 'Dados inválidos.' });
                    else alert('Falha: ' + (data.message || 'Dados inválidos.'));
                }
            },

            render() {
                this.cartContainer.innerHTML = '';
                let totalCents = 0;

                this.cart.forEach((item, index) => {
                    // Check logic for price
                    let appliedPrice = item.originalPriceCents;
                    let hasDiscount = false;
                    
                    if (this.customerIsClub && item.clubPriceCents !== null) {
                        appliedPrice = item.clubPriceCents;
                        hasDiscount = true;
                    }

                    const rowTotal = appliedPrice * item.quantity;
                    totalCents += rowTotal;

                    const row = document.createElement('div');
                    row.style.cssText = 'display: flex; justify-content: space-between; padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.95rem; animation: slideIn 0.2s ease-out; background: rgba(255,255,255,0.02); margin-bottom: 2px; border-radius: 6px;';
                    row.innerHTML = `
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #f1f5f9;">${item.name}</div>
                            <div style="color: #94a3b8; font-size: 0.8rem; margin-top: 0.25rem; display:flex; align-items:center; gap: 0.5rem;">
                                <span style="background: rgba(56,189,248,0.1); color: #38bdf8; padding: 0.1rem 0.4rem; border-radius: 4px; border: 1px solid rgba(56,189,248,0.2);">${item.quantity} x</span> 
                                ${hasDiscount ? `<span style="text-decoration:line-through; color:#64748b; font-size:0.7rem;">${formatMoney(item.originalPriceCents)}</span> <span style="color:#10b981; font-weight:bold;">${formatMoney(appliedPrice)} (VIP)</span>` : formatMoney(appliedPrice)}
                            </div>
                        </div>
                        <div style="font-weight: 700; color: #e2e8f0; text-align: right; display:flex; align-items:center;">
                            ${formatMoney(rowTotal)}
                        </div>
                    `;
                    this.cartContainer.appendChild(row);
                });

                if (this.cart.length === 0) {
                    this.cartContainer.innerHTML = '<div style="padding: 2rem; color: #94a3b8; text-align: center;">Caixa Livre.<br>Passe o Produto.</div>';
                }

                this.subtotalEl.innerText = formatMoney(totalCents);
            },

            cashMovement(type) {
                document.getElementById('movement-type').value = type;
                document.getElementById('movement-modal-title').innerText = type === 'SANGRIA' ? '🩸 Sangria de Caixa' : '🏦 Reforço de Caixa';
                document.getElementById('movement-amount').value = '';
                document.getElementById('movement-reason').value = '';
                document.getElementById('movement-pin').value = '';
                
                const dialog = document.getElementById('movement-modal');
                dialog.showModal();
            }
        };

        window.PosApp = PosApp;
        window.addEventListener('DOMContentLoaded', () => PosApp.init());
    </script>

    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .catalog-item { cursor: pointer; transition: transform 0.1s; }
        .catalog-item:active { transform: scale(0.95); border-color: var(--primary); }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .glass-btn {
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            font-weight: 600;
            padding: 1rem 0;
            cursor: pointer;
        }
        .glass-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            filter: brightness(1.2);
        }
        .glass-btn:active {
            transform: translateY(1px);
        }
        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            100% { transform: scale(1.1); opacity: 1; }
        }
    </style>
</x-layouts.pos>
