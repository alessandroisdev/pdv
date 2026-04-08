<x-layouts.app>
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl fw-bold text-primary">Inserir Carga Recebida (NF-e)</h2>
            <p class="text-light">Bipe os produtos físicos para adicionar ao Romaneio de Entrada.</p>
        </div>
        <a href="{{ route('purchasing.orders.index') }}" class="btn btn-outline" style="border: none; padding: 0.5rem;">
            <i class="fa fa-arrow-left"></i> Voltar para Pedidos
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4 font-semibold shadow-sm border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem;">
        
        <!-- Lado Esquerdo: Cabeçalho NF -->
        <div style="grid-column: span 1; padding-right: 1rem;">
            <form id="master-form" action="{{ route('purchasing.orders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="items" id="items_payload" required>

                <div class="card mb-4">
                    <div class="card-header bg-slate-50">
                        <h3 class="font-bold text-primary">1. Dados do Fornecedor</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Emitente / Fornecedor *</label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">-- Selecione o Favorecido --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->company_name }} ({{ $sup->cnpj_cpf }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Número da Nota Fiscal (NF-e)</label>
                            <input type="text" name="invoice_number" class="form-control" style="font-family: monospace; text-transform: uppercase;" placeholder="Ex: 000.123.456-78">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Observações / Romaneio</label>
                            <textarea name="notes" class="form-control" style="min-height: 80px;" placeholder="Ex: Entrega feita via transportadora XYZ faltou 1 volume."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card" style="border: 2px solid #8e9ccf; background-color: #f8faff;">
                    <div class="card-body" style="text-align: center; padding: 2rem 1.5rem;">
                        <h3 style="font-size: 0.875rem; font-weight: bold; color: #455073; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total do Lote</h3>
                        <div id="display-total" style="font-size: 2.25rem; font-weight: 900; color: #2e3650; margin-bottom: 1.5rem;">R$ 0,00</div>
                        <button type="button" onclick="submitCart()" class="btn btn-primary" style="width: 100%; padding: 0.875rem; font-size: 1.1rem; font-weight: bold;">
                            Arquivar Rascunho NF-e
                        </button>
                        <p class="text-light" style="font-size: 0.75rem; margin-top: 1rem;">Não altera estoque até o Recebimento ser validado.</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lado Direito: Scanner e Itens da Nota -->
        <div style="grid-column: span 2;">
            <div class="card mb-4" style="background-color: #1e293b; color: white;">
                <div class="card-body" style="display: flex; gap: 1rem; align-items: center; position: relative;">
                    <!-- Efeito Visual Laser Scanner -->
                    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background-color: #ef4444; box-shadow: 0 0 15px rgba(239, 68, 68, 1);"></div>
                    
                    <div style="flex: 1; padding-left: 10px;">
                        <label style="color: #cbd5e1; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Bipar Produto (Cód. Barras)</label>
                        <input type="text" id="scanner-input" style="width: 100%; background-color: #0f172a; color: #34d399; font-family: monospace; font-size: 1.25rem; padding: 1rem; border-radius: 8px; border: 2px solid #334155; outline: none; transition: border-color 0.2s;" placeholder="Aguardando feixe laser..." autofocus onkeypress="handleScanner(event)">
                    </div>
                    
                    <div style="text-align: center;">
                        <span style="color: #64748b; display: block; font-size: 0.75rem; font-weight: bold; margin-bottom: 0.5rem;">OU</span>
                        <button type="button" onclick="document.getElementById('modal-search').showModal()" class="btn" style="background-color: #334155; color: white; border: none; padding: 1rem 1.5rem; font-weight: bold; border-radius: 8px;">
                            <i class="fa fa-keyboard"></i> Busca Teclado
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabela Vazia Ghost State -->
            <div id="empty-cart-msg" class="card" style="border: 2px dashed #cbd5e1; box-shadow: none; display: block;">
                <div class="card-body" style="text-align: center; padding: 4rem 2rem;">
                    <i class="fa fa-barcode" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #94a3b8;">Nenhum Volume Lançado</h3>
                    <p style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.25rem;">Bipe um código de barras para começar a empilhar.</p>
                </div>
            </div>

            <!-- Carrinho List -->
            <div id="cart-table-wrapper" style="display: none;">
                <div class="card" style="overflow-x: auto;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.75rem; text-transform: uppercase;">
                            <tr>
                                <th style="padding: 1rem; width: 40px;">Cx</th>
                                <th style="padding: 1rem;">SKU / Produto Ativo</th>
                                <th style="padding: 1rem; width: 120px; text-align: center;">Quantia</th>
                                <th style="padding: 1rem; width: 140px; text-align: right;">Novo Custo (R$)</th>
                                <th style="padding: 1rem; width: 120px; text-align: right;">Subtotal</th>
                                <th style="padding: 1rem; width: 60px;"></th>
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
                wrapper.style.display = 'none';
                empty.style.display = 'block';
                tTotal.innerText = 'R$ 0,00';
                payload.value = '';
                return;
            }

            wrapper.style.display = 'block';
            empty.style.display = 'none';

            let totalCents = 0;
            let html = '';

            cart.forEach((item, idx) => {
                const sub = item.quantity * item.unit_price_cents;
                totalCents += sub;

                html += `
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                        <td style="padding: 1rem; color: #94a3b8; font-family: monospace; font-size: 0.75rem;">
                            ${(idx+1).toString().padStart(2, '0')}
                        </td>
                        <td style="padding: 1rem;">
                            <span style="font-weight: bold; color: #1e293b; display: block;">${item.name}</span>
                            <span style="font-size: 0.75rem; color: #94a3b8; font-family: monospace; margin-top: 2px; display: inline-block;">${item.barcode || item.sku}</span>
                        </td>
                        <td style="padding: 1rem;">
                            <input type="number" min="1" value="${item.quantity}" onchange="checkNumericInput(event, ${idx}, 'quantity')" class="form-control" style="text-align: center; font-weight: bold;">
                        </td>
                        <td style="padding: 1rem;">
                            <input type="text" data-type="currency" value="${(item.unit_price_cents/100).toFixed(2)}" onchange="checkNumericInput(event, ${idx}, 'unit_price_cents')" class="form-control" style="text-align: right; font-weight: 600;">
                        </td>
                        <td style="padding: 1rem; text-align: right; font-weight: 900; color: #1e293b; background-color: #f8fafc;">
                            ${formatBRL(sub)}
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <button type="button" onclick="removeFromCart(${item.id})" style="color: #f87171; background: none; border: none; cursor: pointer;" title="Excluir Lote">
                                <i class="fa fa-trash"></i>
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
