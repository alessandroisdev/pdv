<x-layouts.pos>
    <!-- Área de Produtos e Leitor -->
    <div class="pos-terminal" style="user-select: none;">
        <div class="pos-search-area">
            <input type="text" id="barcode-input" placeholder="🛒 Bipador de Código de Barras (Mantenha o Foco Aqui) ..." autocomplete="off" autofocus>
        </div>

        <div style="margin-bottom: 1rem;">
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.1rem; color: var(--primary); font-weight: 700;">Atalhos de Balcão</h2>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-outline" style="font-size:0.8rem; border-color: #ef4444; color: #ef4444;" onclick="window.PosApp.cashMovement('SANGRIA')">🩸 SANGRIA</button>
                <button type="button" class="btn btn-outline" style="font-size:0.8rem; border-color: #10b981; color: #10b981;" onclick="window.PosApp.cashMovement('REFORCO')">🏦 REFORÇO</button>
                <a href="{{ route('sales.pos.close_screen') }}" class="btn btn-primary" style="font-size:0.8rem; background: #0f172a;">FECHAR CAIXA</a>
            </div>
        </div>

        <!-- Elegante Modal de Movimentação de Dinheiro -->
        <dialog id="movement-modal" style="padding: 0; border: none; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); width: 400px; max-width: 90vw;">
            <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display:flex; justify-content: space-between; align-items:center;">
                <h3 id="movement-modal-title" style="margin:0; font-size: 1.2rem; color: #0f172a; font-weight:700;">Nova Movimentação</h3>
                <button type="button" onclick="document.getElementById('movement-modal').close()" style="background:transparent; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
            </div>
            <form id="cash-movement-form" method="POST" action="{{ route('sales.pos.movement') }}" style="padding: 1.5rem;">
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
                <div class="pos-product-card catalog-item" 
                     data-id="{{ $product->id }}" 
                     data-name="{{ $product->name }}" 
                     data-price="{{ $product->sale_price->getCents() }}"
                     data-barcode="{{ $product->barcode ?? str_pad($product->id, 8, '0', STR_PAD_LEFT) }}">
                     
                    <h3>{{ \Illuminate\Support\Str::limit($product->name, 25) }}</h3>
                    <div class="price">{{ clone $product->sale_price }}</div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); margin-top:0.5rem;">Estoque: {{ $product->current_stock }}</span>
                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 2rem; text-align: center; color: var(--text-secondary);">
                    Nenhum produto cadastrado no banco de inventário ainda.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Carrinho Analítico (Cupom Fiscal) -->
    <aside class="pos-cart">
        <div style="padding: 1rem; background: var(--bg-light); border-bottom: 1px solid var(--border); text-align: center;">
            <strong style="color: var(--primary);">CUPOM FISCAL LIVRE</strong>
        </div>

        <!-- Renderizado pelo TypeScript PosManager -->
        <div class="pos-cart-items" id="cart-items-container">
            <!-- Vazio por Padrão -->
        </div>

        <div class="pos-cart-summary">
            <div class="summary-row">
                <span>SUBTOTAL</span>
                <strong id="cart-subtotal">R$ 0,00</strong>
            </div>

            <!-- Botões Acionam o Checkout via Typescript que injeta num formulário Ajax -->
            <div class="pos-actions">
                <button type="button" class="btn-pos btn-cash" data-method="Dinheiro" style="grid-column: span 2;">💳 [F2] DINHEIRO FÍSICO</button>
                <button type="button" class="btn-pos" style="background-color: #3b82f6;" data-method="Credito">CARTÃO CRÉDITO</button>
                <button type="button" class="btn-pos" style="background-color: #0ea5e9;" data-method="Debito">CARTÃO DÉBITO</button>
                <button type="button" class="btn-pos btn-pix" data-method="Pix" style="grid-column: span 2;">RECEBER VIA PIX</button>
            </div>
            
            <!-- Hidden Form to BackEnd PointOfSaleController -->
            <form id="checkout-form" method="POST" action="{{ route('sales.pos.checkout') }}" style="display:none;">
                @csrf
                <input type="hidden" name="payload_json" id="checkout-payload" value="">
            </form>
        </div>
    </aside>

    @if(session('sale_id'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const url = "{{ route('sales.pos.receipt', session('sale_id')) }}";
                const popup = window.open(url, 'ImpressaoCupom', 'width=380,height=600,scrollbars=yes,resizable=no');
                if(popup) popup.focus();
            });
        </script>
    @endif

    <script>
        // Core POS Javascript Vanilla (No Bundler Required)
        const formatMoney = (cents) => {
            return 'R$ ' + (cents / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const PosApp = {
            cart: [],
            
            init() {
                this.barcodeInput = document.getElementById('barcode-input');
                this.cartContainer = document.getElementById('cart-items-container');
                this.subtotalEl = document.getElementById('cart-subtotal');
                this.form = document.getElementById('checkout-form');
                this.payloadField = document.getElementById('checkout-payload');
                
                // Keep Focus Loop
                document.addEventListener('click', (e) => {
                    if(!e.target.closest('button') && !e.target.closest('a') && e.target.tagName !== 'INPUT') {
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

                // Catalog Clicks Listener
                document.querySelectorAll('.catalog-item').forEach(card => {
                    card.addEventListener('click', () => {
                        this.addToCart(
                            parseInt(card.dataset.id),
                            card.dataset.name,
                            parseInt(card.dataset.price)
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
                // Find in catalog
                const card = document.querySelector(`.catalog-item[data-barcode="${code}"]`);
                if (card) {
                    this.addToCart(parseInt(card.dataset.id), card.dataset.name, parseInt(card.dataset.price));
                } else {
                    alert('Código não encontrado no catálogo pré-carregado!');
                }
            },

            addToCart(id, name, priceCents) {
                const existing = this.cart.find(i => i.id === id);
                if (existing) {
                    existing.quantity++;
                } else {
                    this.cart.unshift({ id, name, priceCents, quantity: 1 }); // unshift to put on top of bill
                }
                this.render();
            },

            clearCart() {
                if(this.cart.length > 0 && confirm('Cancelar cupom atual?')) {
                    this.cart = [];
                    this.render();
                }
            },

            checkout(method) {
                if (this.cart.length === 0) {
                    alert('Carrinho vazio!');
                    return;
                }
                
                this.payloadField.value = JSON.stringify({
                    payment_method: method,
                    items: this.cart.map(i => ({ id: i.id, quantity: i.quantity }))
                });
                
                console.log("Submitting Checkout:", this.payloadField.value);
                this.form.submit();
            },

            render() {
                this.cartContainer.innerHTML = '';
                let totalCents = 0;

                this.cart.forEach((item, index) => {
                    const rowTotal = item.priceCents * item.quantity;
                    totalCents += rowTotal;

                    const row = document.createElement('div');
                    row.style.cssText = 'display: flex; justify-content: space-between; padding: 0.75rem 1rem; border-bottom: 1px dashed #cbd5e1; font-size: 0.9rem; animation: slideIn 0.2s ease-out;';
                    row.innerHTML = `
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b;">${item.name}</div>
                            <div style="color: #64748b; font-size: 0.8rem;">${item.quantity} x ${formatMoney(item.priceCents)}</div>
                        </div>
                        <div style="font-weight: 700; color: #0f172a; text-align: right;">
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
    </style>
</x-layouts.pos>
