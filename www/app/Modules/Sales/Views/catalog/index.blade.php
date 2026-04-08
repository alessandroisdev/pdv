<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Catálogo Digital | Autoatendimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .cart-enter { transform: translateY(100%); }
        .cart-enter-active { transform: translateY(0); transition: transform 0.3s ease-out; }
        .cart-leave { transform: translateY(0); }
        .cart-leave-active { transform: translateY(100%); transition: transform 0.3s ease-in; }
    </style>
</head>
<body x-data="catalogApp()" class="antialiased pb-24">

    <!-- Header Fixed -->
    <header class="bg-white shadow-sm sticky top-0 z-40 border-b border-rose-100">
        <div class="px-4 py-3 flex justify-between items-center">
            <h1 class="text-xl font-black text-slate-800 tracking-tight">
                <i class="fa fa-hamburger text-rose-500 mr-2"></i> Cardápio Digital
            </h1>
            <button @click="cartOpen = true" class="relative p-2 text-slate-600 focus:outline-none">
                <i class="fa fa-shopping-bag text-xl inline-block -mt-1"></i>
                <span x-show="totalItems > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-rose-600 rounded-full" x-text="totalItems"></span>
            </button>
        </div>
    </header>

    <!-- App Content -->
    <main class="p-4">
        <!-- Hero Banner Opcional -->
        <div class="bg-gradient-to-r from-rose-500 to-rose-600 rounded-2xl p-6 mb-6 text-white shadow-lg shadow-rose-200">
            <h2 class="text-2xl font-bold mb-1">Faça seu Pedido!</h2>
            <p class="text-sm opacity-90">Rápido, prático e direto pra cozinha.</p>
        </div>

        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-200 pb-2">Nossos Produtos</h3>
        
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
            @foreach($products as $p)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col justify-between">
                <div>
                    <!-- Imagem Dummy ou Real -->
                    <div class="h-32 bg-slate-100 flex items-center justify-center">
                        <i class="fa fa-utensils text-4xl text-slate-300"></i>
                    </div>
                    <div class="p-3">
                        <h4 class="font-bold text-slate-800 text-sm leading-tight mb-1">{{ $p->name }}</h4>
                        <div class="text-xs font-mono text-slate-400 mb-2">Cód: {{ $p->barcode ?? str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="font-black text-indigo-600">{{ new App\Modules\Core\ValueObjects\Money($p->price_cents) }}</div>
                    </div>
                </div>
                <div class="p-3 pt-0 mt-auto">
                    <!-- Selector Quantity / Add Button -->
                    <button @click="addToCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price_cents }})" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors flex justify-center items-center gap-2">
                        <i class="fa fa-plus text-xs"></i> Adicionar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    <!-- Bottom Floating Sheet (Carrinho Widget) -->
    <div x-show="totalItems > 0 && !cartOpen" 
         x-transition.opacity
         class="fixed bottom-4 left-4 right-4 z-40 bg-zinc-900 rounded-xl shadow-2xl p-4 flex justify-between items-center text-white cursor-pointer"
         @click="cartOpen = true">
        <div class="flex items-center gap-3">
            <div class="bg-rose-500 w-10 h-10 rounded-full flex justify-center items-center font-bold text-lg" x-text="totalItems"></div>
            <div>
                <div class="text-sm font-semibold text-zinc-300">Ver Carrinho</div>
                <div class="font-black text-lg" x-text="formatMoney(cartTotal)"></div>
            </div>
        </div>
        <i class="fa fa-chevron-up text-zinc-500"></i>
    </div>

    <!-- Carrinho Modal Fullscreen -->
    <div x-show="cartOpen" style="display: none;" class="fixed inset-0 z-50 overflow-hidden flex flex-col bg-slate-50"
         x-transition:enter="cart-enter-active"
         x-transition:enter-start="cart-enter"
         x-transition:enter-end="cart-leave"
         x-transition:leave="cart-leave-active"
         x-transition:leave-start="cart-leave"
         x-transition:leave-end="cart-enter">
        
        <header class="bg-white px-4 py-4 flex items-center justify-between border-b border-slate-200 shadow-sm">
            <div class="flex items-center gap-2 text-lg font-bold text-slate-800">
                <i class="fa fa-shopping-cart text-rose-500 text-xl"></i>
                Seu Pedido
            </div>
            <button @click="cartOpen = false" class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 hover:bg-slate-200">
                <i class="fa fa-times"></i>
            </button>
        </header>

        <div class="flex-1 overflow-y-auto p-4 content-body">
            
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-slate-400">
                    <i class="fa fa-shopping-basket text-6xl mb-4 text-slate-200"></i>
                    <p class="font-medium text-lg">Seu carrinho está vazio.</p>
                    <button @click="cartOpen = false" class="mt-4 px-6 py-2 bg-slate-200 text-slate-700 rounded-full font-bold">Voltar ao Cardápio</button>
                </div>
            </template>
            
            <template x-if="cart.length > 0">
                <ul class="divide-y divide-slate-100">
                    <template x-for="item in cart" :key="item.id">
                        <li class="py-4 flex justify-between items-center">
                            <div class="flex-1 pr-4">
                                <h4 class="font-bold text-slate-800" x-text="item.name"></h4>
                                <div class="text-indigo-600 font-bold font-mono text-sm" x-text="formatMoney(item.price_cents)"></div>
                            </div>
                            <div class="flex items-center gap-3 bg-slate-100 rounded-full p-1">
                                <button @click="decrement(item.id)" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-slate-600 shadow-sm"><i class="fa fa-minus text-xs"></i></button>
                                <span class="font-bold w-4 text-center" x-text="item.qty"></span>
                                <button @click="increment(item.id)" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-slate-600 shadow-sm"><i class="fa fa-plus text-xs"></i></button>
                            </div>
                        </li>
                    </template>
                </ul>
            </template>
        </div>

        <div x-show="cart.length > 0" class="bg-white border-t border-slate-200 p-4 pb-8 md:pb-4 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.05)]">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Forma de Pagamento (No Balcão)</label>
                <select x-model="paymentMethod" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 font-semibold text-slate-800 text-sm">
                    <option value="PIX">Pix / QR Code</option>
                    <option value="CREDIT_CARD">Cartão de Crédito</option>
                    <option value="DEBIT_CARD">Cartão de Débito</option>
                    <option value="MONEY">Dinheiro em Espécie</option>
                </select>
            </div>
            
            <div class="flex justify-between items-center mb-4">
                <span class="text-slate-500 font-semibold">Total do Pedido:</span>
                <span class="text-2xl font-black text-emerald-600" x-text="formatMoney(cartTotal)"></span>
            </div>
            
            <button 
                @click="submitOrder()" 
                :disabled="isSubmitting"
                class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-black text-lg py-4 rounded-xl shadow-lg shadow-emerald-200 flex justify-center items-center gap-2 transition-all">
                <i class="fa" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check-circle'"></i>
                <span x-text="isSubmitting ? 'Enviando pra cozinha...' : 'Confirmar Pedido'"></span>
            </button>
        </div>

    </div>

    <!-- Success Modal -->
    <div x-show="showSuccess" style="display: none;" class="fixed inset-0 z-[60] bg-emerald-600 flex flex-col items-center justify-center text-white p-6 text-center">
        <i class="fa fa-check-circle text-8xl mb-6 shadow-emerald-800 drop-shadow-lg"></i>
        <h2 class="text-3xl font-black mb-2">Tudo Certo!</h2>
        <p class="text-lg opacity-90 mb-8" x-text="successMessage"></p>
        <button @click="resetApp()" class="px-8 py-3 bg-white text-emerald-700 font-bold rounded-full shadow-lg">Fazer novo pedido</button>
    </div>

    <script>
        function catalogApp() {
            return {
                cartOpen: false,
                cart: [],
                paymentMethod: 'PIX',
                isSubmitting: false,
                showSuccess: false,
                successMessage: '',

                get totalItems() {
                    return this.cart.reduce((total, item) => total + item.qty, 0);
                },

                get cartTotal() {
                    return this.cart.reduce((total, item) => total + (item.price_cents * item.qty), 0);
                },

                formatMoney(cents) {
                    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cents / 100);
                },

                addToCart(id, name, price) {
                    const exists = this.cart.find(i => i.id === id);
                    if (exists) {
                        exists.qty++;
                    } else {
                        this.cart.push({ id, name, price_cents: price, qty: 1 });
                    }
                    
                    // Pequena animação tátil / haptics feedback
                    if (navigator.vibrate) navigator.vibrate(50);
                },

                increment(id) {
                    const item = this.cart.find(i => i.id === id);
                    if (item) item.qty++;
                },

                decrement(id) {
                    const item = this.cart.find(i => i.id === id);
                    if (item) {
                        item.qty--;
                        if (item.qty <= 0) {
                            this.cart = this.cart.filter(i => i.id !== id);
                        }
                    }
                },

                async submitOrder() {
                    if (this.cart.length === 0) return;
                    this.isSubmitting = true;

                    const payload = {
                        customer_name: 'Novo Cliente Totem',
                        payment_method: this.paymentMethod,
                        items: this.cart.map(i => ({
                            product_id: i.id,
                            quantity: i.qty
                        }))
                    };

                    try {
                        const response = await fetch('{{ route("catalog.checkout") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.successMessage = data.message || "Pedido enviado à matriz!";
                            this.showSuccess = true;
                        } else {
                            alert('Erro: ' + (data.message || 'Falha ao processar o pedido.'));
                        }
                    } catch (e) {
                        alert('Erro de Conexão com o Caixa Matriz.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                resetApp() {
                    this.cart = [];
                    this.cartOpen = false;
                    this.showSuccess = false;
                    window.scrollTo(0,0);
                }
            }
        }
    </script>
</body>
</html>
