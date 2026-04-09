<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Catálogo Omnichannel</title>
    <!-- Tailwind CSS (CDN for standalone PWA decoupled from backoffice layout) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; padding-bottom: 80px; }
        .cart-badge {
            position: absolute; top: -5px; right: -5px; background: #ef4444; color: white;
            border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center;
            justify-content: center; font-size: 10px; font-weight: bold;
        }
    </style>
</head>
<body class="text-slate-800">

    <!-- App Header -->
    <header class="bg-indigo-600 text-white shadow-md sticky top-0 z-40">
        <div class="px-4 py-3 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Nosso Catálogo</h1>
                <p class="text-xs text-indigo-200">Faça seu pedido diretamente pelo celular</p>
            </div>
            <button onclick="toggleCart()" class="relative p-2 bg-indigo-700 rounded-full hover:bg-indigo-800 transition">
                <i class="fa fa-shopping-bag text-lg"></i>
                <span id="cart-counter" class="cart-badge hidden">0</span>
            </button>
        </div>
    </header>

    <!-- Product Grid -->
    <main class="p-4 grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($products as $product)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="bg-slate-100 h-32 flex items-center justify-center p-4">
                    <i class="fa fa-box text-4xl text-slate-300"></i>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-800 leading-tight mb-1">{{ $product->name }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $product->description ?? 'Sem descrição' }}</p>
                    </div>
                    <div>
                        <div class="font-black text-emerald-600 mb-2">R$ {{ number_format($product->price_cents_sale / 100, 2, ',', '.') }}</div>
                        <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price_cents_sale }})" 
                                class="w-full bg-indigo-50 text-indigo-600 font-bold py-2 rounded text-sm hover:bg-indigo-100 transition">
                            <i class="fa fa-plus"></i> Adicionar
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </main>

    <!-- Floating Action Button for Cart (Mobile Bottom) -->
    <div id="bottom-cart-bar" class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] transform transition-transform translate-y-full z-40">
        <div class="flex justify-between items-center max-w-lg mx-auto">
            <div>
                <div class="text-xs font-bold text-slate-500 uppercase">Total do Pedido</div>
                <div id="cart-total-display" class="font-black text-xl text-slate-800">R$ 0,00</div>
            </div>
            <button onclick="toggleCart()" class="bg-indigo-600 text-white font-bold px-6 py-3 rounded-lg shadow-md hover:bg-indigo-700">
                Ver Sacola <i class="fa fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>

    <!-- Cart Modal Overlay -->
    <div id="cart-modal" class="fixed inset-0 bg-slate-900/60 z-50 hidden flex justify-end">
        <div class="bg-white w-full max-w-sm h-full shadow-2xl flex flex-col">
            <div class="px-4 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="font-bold text-lg"><i class="fa fa-shopping-bag text-indigo-500 mr-2"></i>Sua Sacola</h2>
                <button onclick="toggleCart()" class="p-2 text-slate-400 hover:text-slate-700"><i class="fa fa-times text-xl"></i></button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4" id="cart-items-container">
                <!-- Injetar via JS -->
                <div class="text-center text-slate-400 mt-10">
                    <i class="fa fa-box-open text-4xl mb-2"></i>
                    <p>Sua sacola está vazia.</p>
                </div>
            </div>

            <div class="p-4 border-t border-slate-200 bg-slate-50">
                <div class="mb-3">
                    <input type="text" id="customer-name" placeholder="Seu Nome (Opcional)" class="w-full px-3 py-2 border border-slate-300 rounded text-sm mb-2">
                    <input type="text" id="table-number" placeholder="Mesa / Comanda" class="w-full px-3 py-2 border border-slate-300 rounded text-sm mb-2">
                </div>
                <div class="flex justify-between text-lg font-bold mb-4">
                    <span>Total:</span>
                    <span id="checkout-total-val" class="text-emerald-600">R$ 0,00</span>
                </div>
                <button onclick="checkout()" id="btn-checkout" class="w-full bg-emerald-600 text-white font-bold py-3 rounded-lg shadow-md hover:bg-emerald-700 transition flex justify-center items-center">
                    <i class="fa fa-check mr-2"></i> Finalizar Pedido
                </button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function addToCart(id, name, priceCents) {
            let item = cart.find(i => i.id === id);
            if (item) {
                item.qty++;
            } else {
                cart.push({ id: id, name: name, priceCents: priceCents, qty: 1 });
            }
            updateCartUI();
        }

        function removeFromCart(id) {
            cart = cart.filter(i => i.id !== id);
            updateCartUI();
        }

        function toggleCart() {
            const modal = document.getElementById('cart-modal');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function updateCartUI() {
            const counter = document.getElementById('cart-counter');
            const totalItems = cart.reduce((acc, obj) => acc + obj.qty, 0);
            
            if (totalItems > 0) {
                counter.innerText = totalItems;
                counter.classList.remove('hidden');
                document.getElementById('bottom-cart-bar').classList.remove('translate-y-full');
            } else {
                counter.classList.add('hidden');
                document.getElementById('bottom-cart-bar').classList.add('translate-y-full');
            }

            let totalCents = cart.reduce((acc, obj) => acc + (obj.priceCents * obj.qty), 0);
            let formattedTotal = 'R$ ' + (totalCents / 100).toLocaleString('pt-BR', {minimumFractionDigits: 2});
            
            document.getElementById('cart-total-display').innerText = formattedTotal;
            document.getElementById('checkout-total-val').innerText = formattedTotal;

            const container = document.getElementById('cart-items-container');
            if (cart.length === 0) {
                container.innerHTML = `<div class="text-center text-slate-400 mt-10"><i class="fa fa-box-open text-4xl mb-2"></i><p>Sua sacola está vazia.</p></div>`;
                return;
            }

            let html = '';
            cart.forEach(item => {
                let itemTotal = 'R$ ' + ((item.priceCents * item.qty) / 100).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                html += `
                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                    <div class="flex-1">
                        <div class="text-sm font-bold text-slate-800">${item.name}</div>
                        <div class="text-xs text-slate-500">${item.qty}x de R$ ${(item.priceCents/100).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-emerald-600 mb-1">${itemTotal}</div>
                        <button onclick="removeFromCart(${item.id})" class="text-xs text-red-500 hover:underline"><i class="fa fa-trash"></i> Remover</button>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        }

        function checkout() {
            if (cart.length === 0) return alert('A sacola está vazia.');

            const btn = document.getElementById('btn-checkout');
            btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Enviando...';
            btn.disabled = true;

            const payload = {
                customer_name: document.getElementById('customer-name').value,
                table_number: document.getElementById('table-number').value,
                cart: cart.map(i => ({ id: i.id, qty: i.qty }))
            };

            fetch('{{ route("catalog.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = '<i class="fa fa-check mr-2"></i> Finalizar Pedido';
                btn.disabled = false;
                
                if (data.error) {
                    alert('Erro ao fechar pedido: ' + data.error);
                } else {
                    alert('Sucesso! O pedido #' + data.sale_id + ' foi enviado para o balcão.');
                    cart = [];
                    updateCartUI();
                    toggleCart();
                }
            })
            .catch(err => {
                btn.innerHTML = '<i class="fa fa-check mr-2"></i> Finalizar Pedido';
                btn.disabled = false;
                alert('Erro na rede. Tente novamente.');
            });
        }
    </script>
</body>
</html>
