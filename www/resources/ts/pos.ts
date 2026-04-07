import Swal from 'sweetalert2';

declare global {
    interface Window {
        Swal: any;
        PosApp: any;
    }
}

window.Swal = Swal;

interface CartItem {
    id: number;
    name: string;
    priceCents: number;
    quantity: number;
}

class PosManager {
    public cart: CartItem[] = [];
    public cartTableId = 'cart-items-container';
    public subtotalId = 'cart-subtotal';

    constructor() {
        this.initListeners();
        this.renderCart();
        
        // Atachar global functions pro Blade
        (window as any).PosApp = this;
    }

    public cashMovement(type: 'SANGRIA' | 'REFORCO') {
        Swal.fire({
            title: type === 'SANGRIA' ? '🩸 Realizar Sangria' : '🏦 Adicionar Reforço',
            html: `
                <input id="swal-amount" class="swal2-input" placeholder="Valor (Ex: 150,00)" inputmode="decimal">
                <input id="swal-reason" class="swal2-input" placeholder="Motivo (Ex: Retirada de Caixa)">
                <input id="swal-pin" type="password" class="swal2-input" placeholder="PIN do Supervisor" inputmode="numeric">
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Registrar Movimento',
            preConfirm: () => {
                const formId = document.getElementById('cash-movement-form') as HTMLFormElement;
                if(formId) {
                    (document.getElementById('movement-amount') as HTMLInputElement).value = (document.getElementById('swal-amount') as HTMLInputElement).value;
                    (document.getElementById('movement-reason') as HTMLInputElement).value = (document.getElementById('swal-reason') as HTMLInputElement).value;
                    (document.getElementById('movement-pin') as HTMLInputElement).value = (document.getElementById('swal-pin') as HTMLInputElement).value;
                    (document.getElementById('movement-type') as HTMLInputElement).value = type;
                    formId.submit();
                }
            }
        });
    }

    public addToCart(id: number, name: string, priceCents: number, qty: number = 1) {
        const existingItem = this.cart.find(i => i.id === id);
        if (existingItem) {
            existingItem.quantity += qty;
        } else {
            this.cart.push({ id, name, priceCents, quantity: qty });
        }
        this.playSound('beep');
        this.renderCart();
    }

    public clearCart() {
        this.cart = [];
        this.renderCart();
    }

    public async removeWithSupervisor(id: number) {
        const { value: pin } = await Swal.fire({
            title: 'Exclusão Restrita',
            text: 'Informe o PIN Numérico do Supervisor:',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                pattern: '[0-9]*',
                inputmode: 'numeric'
            },
            showCancelButton: true,
            confirmButtonText: 'Autorizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444'
        });

        if (pin) {
            Swal.showLoading();
            const token = (document.querySelector('form#checkout-form input[name="_token"]') as HTMLInputElement)?.value;
            
            try {
                const response = await fetch('/vendas/pdv/supervisor-override', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ pin })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.cart = this.cart.filter(i => i.id !== id);
                    this.playSound('beep');
                    this.renderCart();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Item Removido.', text: 'Auorizado por: ' + data.supervisor_name, showConfirmButton: false, timer: 3000 });
                } else {
                    Swal.fire('Não Autorizado', data.message || 'Senha incorreta', 'error');
                }
            } catch (error) {
                Swal.fire('Erro', 'Falha na comunicação base', 'error');
            }
        }
    }

    public getSubtotalCents(): number {
        return this.cart.reduce((acc, item) => acc + (item.priceCents * item.quantity), 0);
    }

    private initListeners() {
        // Escutar cliques em botões de atalho da vitrine do balcão
        document.querySelectorAll('.pos-product-card').forEach(card => {
            card.addEventListener('click', (e) => {
                const el = e.currentTarget as HTMLElement;
                const id = parseInt(el.dataset.id || '0');
                const name = el.dataset.name || 'Produto';
                const price = parseInt(el.dataset.price || '0');
                
                if(id > 0) {
                    this.addToCart(id, name, price, 1);
                }
            });
        });

        // Simular leitor de código de barras
        const barcodeInput = document.getElementById('barcode-input') as HTMLInputElement;
        if(barcodeInput) {
            barcodeInput.addEventListener('keypress', (e) => {
                if(e.key === 'Enter') {
                    e.preventDefault();
                    const code = barcodeInput.value;
                    // TODO: Fazer match com JSON do blade de produtos via dataset
                    Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, title: 'Buscando: ' + code });
                    barcodeInput.value = '';
                }
            });
            // Auto focus
            barcodeInput.focus();
            document.addEventListener('click', () => {
                if(!window.getSelection()?.toString() && document.activeElement?.tagName !== 'INPUT') {
                    barcodeInput.focus();
                }
            });
        }
        
        // Finalizações Genéricas
        document.querySelectorAll('.btn-pos').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const method = (e.currentTarget as HTMLElement).dataset.method;
                this.checkout(method || 'DINHEIRO');
            });
        });
    }

    private renderCart() {
        const container = document.getElementById(this.cartTableId);
        const subLabel = document.getElementById(this.subtotalId);
        
        if(!container) return;
        
        container.innerHTML = '';
        
        if (this.cart.length === 0) {
            container.innerHTML = `<div style="text-align:center; padding: 2rem; color: #94a3b8;">Nenhum produto escaneado ainda.</div>`;
        } else {
            this.cart.forEach(item => {
                const itemTotal = (item.priceCents * item.quantity) / 100;
                
                const div = document.createElement('div');
                div.className = 'cart-item';
                div.style.display = 'flex';
                div.style.alignItems = 'center';
                div.innerHTML = `
                    <div class="item-info" style="flex: 1;">
                        <h4>${item.name}</h4>
                        <div class="item-meta"><span>${item.quantity}x</span> R$ ${(item.priceCents/100).toFixed(2).replace('.', ',')}</div>
                    </div>
                    <div class="item-total" style="font-weight: 700; margin-right: 1rem;">
                        R$ ${itemTotal.toFixed(2).replace('.', ',')}
                    </div>
                    <button class="btn btn-outline" style="border: 1px solid #ef4444; color: #ef4444; padding: 0.25rem 0.6rem; font-weight: bold; cursor: pointer;" onclick="window.PosApp.removeWithSupervisor(${item.id})">X</button>
                `;
                container.appendChild(div);
            });
        }

        // Atualizar visual do Subtotal
        if (subLabel) {
            const total = this.getSubtotalCents() / 100;
            subLabel.innerHTML = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        // Rolar pra baixo sempre
        container.scrollTop = container.scrollHeight;
    }

    private playSound(type: string) {
        // Sons audíveis gerados com Audio Context puro (Navegador) - dispensando mp3 asset externo para agilidade!
        try {
            const ctx = new (window.AudioContext || (window as any).webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            if (type === 'beep') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800, ctx.currentTime);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                osc.start();
                osc.stop(ctx.currentTime + 0.1);
            } else if (type === 'success') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(600, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                osc.start();
                osc.stop(ctx.currentTime + 0.15);
            }
        } catch (e) {
            // Silencioso se navegador bloquear
        }
    }

    private checkout(method: string) {
        if(this.cart.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Carrinho Vazio', text: 'Passe pelo menos um produto.' });
            return;
        }
        
        Swal.fire({
            title: `Receber em ${method}?`,
            text: `Subtotal de R$ ${(this.getSubtotalCents()/100).toFixed(2).replace('.', ',')}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirmar & Imprimir',
            cancelButtonText: 'Voltar',
            confirmButtonColor: '#10b981'
        }).then(result => {
             if(result.isConfirmed) {
                 this.playSound('success');
                 Swal.fire({
                     title: 'Processando Venda...',
                     allowOutsideClick: false,
                     didOpen: () => { Swal.showLoading(); }
                 });
                 
                 // Payload Form via fetch p/ Back-end Laravel!
                 const payload = {
                     payment_method: method,
                     total_cents: this.getSubtotalCents(),
                     items: this.cart
                 };
                 
                 const formId = document.getElementById('checkout-form') as HTMLFormElement;
                 const payloadInput = document.getElementById('checkout-payload') as HTMLInputElement;
                 if(formId && payloadInput) {
                     payloadInput.value = JSON.stringify(payload);
                     formId.submit();
                 }
             }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Injetar sistema visual
    window.PosApp = new PosManager();
});
