<x-layouts.pos>
    <!-- Área de Produtos e Leitor -->
    <div class="pos-terminal">
        <div class="pos-search-area">
            <input type="text" id="barcode-input" placeholder="Digite o Código de Barras e aperte Enter..." autocomplete="off">
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

        <form id="cash-movement-form" method="POST" action="{{ route('sales.pos.movement') }}" style="display:none;">
            @csrf
            <input type="hidden" name="type" id="movement-type">
            <input type="hidden" name="amount" id="movement-amount">
            <input type="hidden" name="reason" id="movement-reason">
            <input type="hidden" name="supervisor_pin" id="movement-pin">
        </form>

        <div class="pos-product-grid">
            <!-- Mock visual que injetará direto no Carrinho Javascript via Dataset ID -->
            @forelse($products as $product)
                <div class="pos-product-card" 
                     data-id="{{ $product->id }}" 
                     data-name="{{ $product->name }}" 
                     data-price="{{ $product->price->getCents() }}">
                     
                    <h3>{{ \Illuminate\Support\Str::limit($product->name, 25) }}</h3>
                    <div class="price">R$ {{ number_format($product->price->getCents() / 100, 2, ',', '.') }}</div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); margin-top:0.5rem;">Estoque: {{ $product->stock }}</span>
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
                // Abre o cupom num popup focado no centro pra impressão térmica
                const popup = window.open(url, 'ImpressaoCupom', 'width=380,height=600,scrollbars=yes,resizable=no');
                if(popup) {
                    popup.focus();
                } else {
                    console.log('Popup bloqueado pelo navegador. Cupom id: {{ session("sale_id") }}');
                }
            });
        </script>
    @endif
</x-layouts.pos>
