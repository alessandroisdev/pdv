<x-layouts.app>
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-2xl fw-bold text-primary">Inserir Carga Recebida (NF-e)</h2>
            <p class="text-slate-500">Bipe os produtos físicos para adicionar ao Romaneio de Entrada.</p>
        </div>
        <a href="{{ route('purchasing.orders.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Voltar para Pedidos
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4 font-semibold shadow-sm border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative">
        
        <!-- Lado Esquerdo: Cabeçalho NF -->
        <div class="lg:col-span-1 border-r border-slate-200 pr-0 lg:pr-6">
            <form id="master-form" action="{{ route('purchasing.orders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="items" id="items_payload" required>

                <div class="card shadow-sm border-0 bg-white mb-6">
                    <div class="card-header bg-slate-50 border-b border-slate-200">
                        <h3 class="font-bold text-slate-700">1. Dados do Fornecedor</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label class="text-sm font-semibold">Emitente / Fornecedor *</label>
                            <select name="supplier_id" class="form-control w-full bg-slate-50" required>
                                <option value="">-- Selecione o Favorecido --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->company_name }} ({{ $sup->cnpj_cpf }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="text-sm font-semibold">Número da Nota Fiscal (NF-e)</label>
                            <input type="text" name="invoice_number" class="form-control w-full font-mono uppercase bg-slate-50" placeholder="Ex: 000.123.456-78">
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-sm font-semibold">Observações / Romaneio</label>
                            <textarea name="notes" class="form-control w-full h-20 bg-slate-50" placeholder="Ex: Entrega feita via transportadora XYZ faltou 1 volume."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-md border-2 border-indigo-100 bg-indigo-50/30">
                    <div class="card-body py-6 text-center">
                        <h3 class="text-sm font-bold text-indigo-800 uppercase tracking-wide mb-1">Total do Lote</h3>
                        <div id="display-total" class="text-4xl font-black text-indigo-900 mb-4">R$ 0,00</div>
                        <button type="button" onclick="submitCart()" class="btn btn-primary w-full py-3 bg-indigo-600 hover:bg-indigo-700 shadow-md transform hover:scale-[1.02] transition-transform text-white font-bold text-lg">
                            Arquivar Rascunho NF-e
                        </button>
                        <p class="text-xs text-slate-500 mt-3 relative">Não altera estoque até o Recebimento ser validado.</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lado Direito: Scanner e Itens da Nota -->
        <div class="lg:col-span-2">
            <div class="bg-slate-800 rounded-xl shadow-inner p-6 mb-6 flex gap-4 items-center relative overflow-hidden">
                <!-- Efeito Visual Laser Scanner -->
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500 shadow-[0_0_15px_rgba(239,68,68,1)]"></div>
                
                <div class="flex-1">
                    <label class="text-slate-300 text-xs font-bold uppercase tracking-wider mb-2 block">Bipar Produto (Cód. Barras)</label>
                    <input type="text" id="scanner-input" class="w-full bg-slate-900 text-emerald-400 font-mono text-xl p-4 rounded-lg border-2 border-slate-700 focus:border-red-500 focus:ring-0 outline-none placeholder-slate-700 transition-colors" placeholder="Aguardando feixe laser..." autofocus onkeypress="handleScanner(event)">
                </div>
                
                <div class="text-center mt-6">
                    <span class="text-slate-500 block text-xs mb-2 font-bold">OU</span>
                    <button type="button" onclick="document.getElementById('modal-search').showModal()" class="btn py-4 px-6 bg-slate-700 hover:bg-slate-600 text-white border-0 font-bold shadow-md rounded-lg flex items-center gap-2">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Busca Teclado
                    </button>
                </div>
            </div>

            <!-- Tabela Vazia Ghost State -->
            <div id="empty-cart-msg" class="text-center py-16 bg-white border border-dashed border-slate-300 rounded-xl">
                <svg class="mx-auto w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="text-lg font-bold text-slate-400">Nenhum Volume Lançado</h3>
                <p class="text-slate-400 text-sm mt-1">Bipe um código de barras para começar a empilhar.</p>
            </div>

            <!-- Carrinho List -->
            <div id="cart-table-wrapper" class="hidden">
                <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-sm bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="p-4 w-10">Cx</th>
                                <th class="p-4">SKU / Produto Ativo</th>
                                <th class="p-4 w-32 text-center">Quantia</th>
                                <th class="p-4 w-36 text-right">Novo Custo (R$)</th>
                                <th class="p-4 w-32 text-right">Subtotal</th>
                                <th class="p-4 w-16"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            <!-- JS Injection -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Busca Secundária (Fallback) -->
    <dialog id="modal-search" class="modal rounded-xl shadow-2xl p-0 overflow-hidden bg-white" style="width: 700px; border: none; outline: none;">
        <div class="p-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50">
            <svg class="text-slate-400" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="manual-search-input" class="w-full bg-transparent border-0 outline-none text-xl font-medium text-slate-800 placeholder-slate-400" placeholder="Digite nome ou código e tecle Enter..." onkeypress="handleManualSearch(event)">
            <button class="text-slate-400 hover:text-slate-600 focus:outline-none" onclick="document.getElementById('modal-search').close()">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="max-h-96 overflow-y-auto p-2" id="search-results">
            <div class="p-10 text-center text-slate-400 text-sm">
                Procurando nos galpões... tecle enter para disparar busca.
            </div>
        </div>
    </dialog>

    <script>
        let cart = [];

        function formatBRL(cents) {
            return (cents / 100).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }

        function parseBRL(stringVal) {
            const num = stringVal.replace(/[^\d,.-]/g, '').replace(/\./g, '').replace(',', '.');
            return Math.round(parseFloat(num || 0) * 100);
        }

        async function handleScanner(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const input = e.target;
                const query = input.value.trim();
                
                if (query === '') return;

                // Bloqueia e mostra scan visual
                input.disabled = true;
                input.style.opacity = '0.5';

                try {
                    const res = await fetch(`/compras/api/produtos?q=${encodeURIComponent(query)}`);
                    const data = await res.json();

                    if (data.length === 1) {
                        // Encontrado exato pelo código de barras - Adiciona instantâneo
                        addToCart(data[0]);
                        window.toast.fire({ icon: 'success', title: '+ ' + data[0].name.substring(0, 15) });
                    } else if (data.length > 1) {
                        // Conflito ou termo amplo (Nome)
                        document.getElementById('manual-search-input').value = query;
                        document.getElementById('modal-search').showModal();
                        renderSearchResults(data);
                    } else {
                        // Falha
                        window.toast.fire({ icon: 'error', title: 'Produto inexistente' });
                        const sound = new Audio('data:audio/wav;base64,UklGRlIAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YTEAAAAA/...bip_error_simulation...'); 
                        // just simple UI fail is fine.
                    }
                } catch(error) {
                    console.error(error);
                }

                input.value = '';
                input.disabled = false;
                input.style.opacity = '1';
                input.focus();
            }
        }

        async function handleManualSearch(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = e.target.value.trim();
                if(!query) return;

                try {
                    const res = await fetch(`/compras/api/produtos?q=${encodeURIComponent(query)}`);
                    const data = await res.json();
                    renderSearchResults(data);
                } catch(error) {
                    console.error(error);
                }
            }
        }

        function renderSearchResults(items) {
            const div = document.getElementById('search-results');
            if(items.length === 0) {
                div.innerHTML = '<div class="p-10 text-center text-slate-400 block font-semibold">Nada encontrado.</div>';
                return;
            }

            let html = '<ul class="divide-y divide-slate-100">';
            items.forEach(item => {
                const encoded = btoa(unescape(encodeURIComponent(JSON.stringify(item))));
                html += `
                    <li class="p-3 hover:bg-slate-50 flex justify-between items-center cursor-pointer group transition-colors" onclick="addFromSearch('${encoded}')">
                        <div>
                            <div class="font-bold text-slate-800">${item.name}</div>
                            <div class="text-xs text-slate-500 font-mono mt-1">CÓD: ${item.barcode || item.sku} | Atual: ${item.stock_quantity ?? 0} unid</div>
                        </div>
                        <div class="text-indigo-600 font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                            + Puxar
                        </div>
                    </li>
                `;
            });
            html += '</ul>';
            div.innerHTML = html;
        }

        function addFromSearch(base64Payload) {
            const item = JSON.parse(decodeURIComponent(escape(atob(base64Payload))));
            addToCart(item);
            document.getElementById('modal-search').close();
            document.getElementById('scanner-input').focus();
        }

        function addToCart(product) {
            // Check if exists
            const existing = cart.find(i => i.id === product.id);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    barcode: product.barcode,
                    quantity: 1,
                    unit_price_cents: product.price_cents_cost > 0 ? product.price_cents_cost : 0
                });
            }
            renderCart();
        }

        function removeFromCart(id) {
            cart = cart.filter(i => i.id !== id);
            renderCart();
        }

        function checkNumericInput(e, index, field) {
            // Replace R$ strings to raw
            let val = e.target.value;
            if (field === 'unit_price_cents') {
                const num = val.replace(/[^\d]/g, '');
                cart[index].unit_price_cents = parseInt(num) || 0;
            } else if (field === 'quantity') {
                cart[index].quantity = parseInt(val) || 1;
            }
            renderCart();
        }

        function renderCart() {
            const wrapper = document.getElementById('cart-table-wrapper');
            const empty = document.getElementById('empty-cart-msg');
            const tbody = document.getElementById('cart-body');
            const tTotal = document.getElementById('display-total');
            const payload = document.getElementById('items_payload');

            if (cart.length === 0) {
                wrapper.classList.add('hidden');
                empty.classList.remove('hidden');
                tTotal.innerText = 'R$ 0,00';
                payload.value = '';
                return;
            }

            wrapper.classList.remove('hidden');
            empty.classList.add('hidden');

            let totalCents = 0;
            let html = '';

            cart.forEach((item, idx) => {
                const sub = item.quantity * item.unit_price_cents;
                totalCents += sub;

                html += `
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 text-slate-400 font-mono text-xs">
                            ${(idx+1).toString().padStart(2, '0')}
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-slate-800 block">${item.name}</span>
                            <span class="text-xs text-slate-400 font-mono mt-0.5 inline-block">${item.barcode || item.sku}</span>
                        </td>
                        <td class="p-4">
                            <input type="number" min="1" value="${item.quantity}" onchange="checkNumericInput(event, ${idx}, 'quantity')" class="w-full text-center border border-slate-200 rounded py-2 outline-none focus:border-indigo-500 bg-white font-bold text-slate-700">
                        </td>
                        <td class="p-4">
                            <input type="text" data-type="currency" value="${(item.unit_price_cents/100).toFixed(2)}" onchange="checkNumericInput(event, ${idx}, 'unit_price_cents')" class="w-full text-right border border-slate-200 rounded py-2 px-3 outline-none focus:border-indigo-500 bg-white font-semibold text-slate-700">
                            <!-- Mascara JS inline simplificada -->
                        </td>
                        <td class="p-4 text-right font-black text-slate-800 bg-slate-50/30">
                            ${formatBRL(sub)}
                        </td>
                        <td class="p-4 text-center">
                            <button type="button" onclick="removeFromCart(${item.id})" class="text-red-400 hover:text-red-600 transition-colors p-2" title="Excluir Lote">
                                <svg w-24 h-24 width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            tTotal.innerText = formatBRL(totalCents);
            
            // Serialize payload for POST
            payload.value = JSON.stringify(cart);

            // Reattach mask simple
            document.querySelectorAll('input[data-type="currency"]').forEach(input => {
                input.addEventListener('blur', function(e) {
                   let raw = this.value.replace(/[^\d.,]/g, '').replace(',', '.');
                   let num = parseFloat(raw);
                   if(isNaN(num)) num = 0;
                   const valCents = Math.round(num * 100);
                   
                   // find index from dom tree
                   const tr = this.closest('tr');
                   const rowIndex = Array.from(tr.parentNode.children).indexOf(tr);
                   
                   cart[rowIndex].unit_price_cents = valCents;
                   renderCart();
                });
            });
        }

        function submitCart() {
            if(cart.length === 0) {
                window.toast.fire({ icon: 'warning', title: 'Carrinho Vazio. Bipe a prateleira primeiro.' });
                return;
            }
            if(!document.querySelector('select[name="supplier_id"]').value) {
                window.toast.fire({ icon: 'warning', title: 'Fornecedor obrigatório!' });
                document.querySelector('select[name="supplier_id"]').focus();
                return;
            }
            document.getElementById('master-form').submit();
        }
    </script>
</x-layouts.app>
