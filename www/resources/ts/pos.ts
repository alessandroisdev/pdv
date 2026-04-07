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
                div.innerHTML = `
                    <div class="item-info">
                        <h4>${item.name}</h4>
                        <div class="item-meta"><span>${item.quantity}x</span> R$ ${(item.priceCents/100).toFixed(2).replace('.', ',')}</div>
                    </div>
                    <div class="item-total">
                        R$ ${itemTotal.toFixed(2).replace('.', ',')}
                    </div>
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
