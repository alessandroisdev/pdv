const API_BASE = 'http://localhost:8000/api/v1';

const PosApp = {
    state: {
        token: localStorage.getItem('pdv_token') || null,
        user: JSON.parse(localStorage.getItem('pdv_user') || 'null'),
        products: JSON.parse(localStorage.getItem('pdv_products') || '[]'),
        cart: [],
        offlineSalesQueue: JSON.parse(localStorage.getItem('pdv_sales_queue') || '[]')
    },

    init() {
        this.setupObservers();
        
        if (this.state.token) {
            document.getElementById('login-overlay').style.display = 'none';
            document.getElementById('operador-nome').innerHTML = `<i class="fa fa-user-check text-emerald-400"></i> ${this.state.user.user_name}`;
            this.renderProducts();
            this.updateCart();
            this.updateSyncCounter();

            // Sincroniza em background ao inicializar (Se houver internet)
            if (navigator.onLine) this.syncDatabaseCache();
        }

        // Monitorar Status de Rede
        window.addEventListener('online', () => this.updateNetworkStatus(true));
        window.addEventListener('offline', () => this.updateNetworkStatus(false));
        this.updateNetworkStatus(navigator.onLine);
    },

    updateNetworkStatus(isOnline) {
        const netEl = document.getElementById('net-status');
        if (isOnline) {
            netEl.className = 'bg-emerald-500 text-white px-2 py-1 rounded shadow-sm';
            netEl.innerHTML = '<i class="fa fa-wifi"></i> ONLINE';
            // Se voltou a internet, tenta sincronizar fila automaticamente
            if (this.state.offlineSalesQueue.length > 0) this.syncWithCloud();
        } else {
            netEl.className = 'bg-red-500 text-white px-2 py-1 rounded shadow-sm';
            netEl.innerHTML = '<i class="fa fa-plane"></i> OFFLINE (Local)';
        }
    },

    showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        toast.innerText = message;
        toast.style.backgroundColor = isError ? '#b91c1c' : '#1e293b';
        toast.style.opacity = '1';
        setTimeout(() => toast.style.opacity = '0', 3000);
    },

    async authenticate() {
        const email = document.getElementById('login-email').value;
        const pass = document.getElementById('login-password').value;
        const errEl = document.getElementById('login-error');

        try {
            const res = await fetch(`${API_BASE}/auth/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: email, password: pass, device_name: 'CAIXA_DESKTOP_1' })
            });

            if (!res.ok) throw new Error('Falha no Login');

            const data = await res.json();
            
            // Grava Credenciais de Bateria Longa no Cache
            this.state.token = data.token;
            this.state.user = data;
            
            localStorage.setItem('pdv_token', data.token);
            localStorage.setItem('pdv_user', JSON.stringify(data));

            this.init();
            
        } catch (e) {
            errEl.style.display = 'block';
        }
    },

    async syncDatabaseCache() {
        if (!navigator.onLine) return; // Não tenta baixar produtos se offline
        
        try {
            const res = await fetch(`${API_BASE}/pos/products`, {
                headers: { 
                    'Authorization': `Bearer ${this.state.token}`,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                const payload = await res.json();
                this.state.products = payload.data;
                localStorage.setItem('pdv_products', JSON.stringify(this.state.products));
                this.renderProducts();
                this.showToast('Catálogo Base Sincronizado do Servidor.');
            }
        } catch (e) {
            console.error('Falha ao espelhar Produtos:', e);
        }
    },

    renderProducts(filter = '') {
        const grid = document.getElementById('catalog-grid');
        grid.innerHTML = '';

        let items = this.state.products;
        if (filter) {
            const f = filter.toLowerCase();
            items = items.filter(p => p.name.toLowerCase().includes(f) || (p.barcode && p.barcode.includes(f)));
        }

        items.forEach(p => {
            const reais = (p.price_cents_sale / 100).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            const badgeCode = p.barcode ? `<div class="text-[0.65rem] font-mono text-slate-400 mt-1"><i class="fa fa-barcode"></i> ${p.barcode}</div>` : '';
            
            const card = document.createElement('div');
            card.className = 'bg-white p-3 border border-slate-200 rounded-lg shadow-sm hover:shadow-md cursor-pointer transition transform hover:-translate-y-1 select-none flex flex-col justify-between';
            card.onclick = () => this.addToCart(p);
            
            card.innerHTML = `
                <div>
                    <h4 class="font-bold text-slate-800 text-sm leading-tight">${p.name}</h4>
                    ${badgeCode}
                </div>
                <div class="mt-3 flex justify-between items-end border-t border-slate-100 pt-2">
                    <span class="text-xs font-bold text-slate-400">Estq: ${p.stock}</span>
                    <span class="text-emerald-600 font-black tracking-tight">${reais}</span>
                </div>
            `;
            grid.appendChild(card);
        });
    },

    filterProducts(val) {
        this.renderProducts(val);
    },

    addToCart(product) {
        const found = this.state.cart.find(c => c.product_id === product.id);
        if (found) {
            found.qty += 1;
        } else {
            this.state.cart.push({ ...product, product_id: product.id, qty: 1 });
        }
        this.updateCart();
    },

    updateCart() {
        const container = document.getElementById('cart-items');
        container.innerHTML = '';
        
        let total_cents = 0;
        let qty = 0;

        this.state.cart.forEach((item, idx) => {
            total_cents += (item.price_cents_sale * item.qty);
            qty += item.qty;

            const linhaCents = item.price_cents_sale * item.qty;
            const linhaStr = (linhaCents / 100).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            
            const div = document.createElement('div');
            div.className = 'flex justify-between items-start mb-3 pb-2 border-b border-dashed border-slate-200';
            div.innerHTML = `
                <div class="flex-1 pr-2">
                    <div class="font-bold text-slate-700 text-sm leading-tight">${item.name}</div>
                    <div class="text-xs text-slate-500 font-mono mt-0.5">${item.qty} un x ${(item.price_cents_sale/100).toFixed(2)}</div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-slate-800">${linhaStr}</div>
                </div>
            `;
            container.appendChild(div);
        });

        document.getElementById('cart-qty').innerText = qty;
        document.getElementById('cart-total').innerText = (total_cents / 100).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },

    checkout(method) {
        if (this.state.cart.length === 0) return;

        let total_cents = this.state.cart.reduce((acc, curr) => acc + (curr.price_cents_sale * curr.qty), 0);

        // Gerar uma Venda Offline (Enqueued)
        const saleRecord = {
            local_id: 'OFFLINE-' + Date.now(),
            total_cents: total_cents,
            payment_method: method,
            items: this.state.cart.map(c => ({ product_id: c.product_id, qty: c.qty }))
        };

        this.state.offlineSalesQueue.push(saleRecord);
        localStorage.setItem('pdv_sales_queue', JSON.stringify(this.state.offlineSalesQueue));

        // Limpa carrinho
        this.state.cart = [];
        this.updateCart();
        this.updateSyncCounter();

        // Integração IPC com Impressora Térmica OS
        if (window.pdvAPI) {
            window.pdvAPI.printReceipt({ id: saleRecord.local_id, total: total_cents });
        }

        this.showToast('Venda Finalizada! (Gaveta Aberta)');

        // Tenta sincronizar silenciosamente se tiver net
        if (navigator.onLine) setTimeout(() => this.syncWithCloud(), 1500);
    },

    updateSyncCounter() {
        document.getElementById('sync-counter').innerText = `${this.state.offlineSalesQueue.length} Vendas Pendentes`;
    },

    async syncWithCloud() {
        if (this.state.offlineSalesQueue.length === 0) return;
        if (!navigator.onLine) {
            this.showToast('Sem Conexão. O sistema faturará quando a internet voltar!', true);
            return;
        }

        document.getElementById('sync-counter').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Subindo...';

        try {
            const res = await fetch(`${API_BASE}/pos/sync-sales`, {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer ${this.state.token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ sales: this.state.offlineSalesQueue })
            });

            if (res.ok) {
                // Esvazia Fila Offline
                this.state.offlineSalesQueue = [];
                localStorage.setItem('pdv_sales_queue', JSON.stringify([]));
                this.updateSyncCounter();
                this.showToast('Nuvem Sincronizada com Sucesso!');
                // Rebaixa Estoque atualizado pós fechamentos
                this.syncDatabaseCache();
            } else {
                throw new Error("Erro na API");
            }
        } catch (e) {
            this.showToast('A Matrix Rejeitou o Lote. Tentaremos depois.', true);
            this.updateSyncCounter();
        }
    },

    setupObservers() {
        // Atalho F2 Dinheiro
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2') { e.preventDefault(); this.checkout('CASH'); }
        });
    }
};

window.onload = () => PosApp.init();
