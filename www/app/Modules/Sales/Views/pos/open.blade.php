<x-layouts.pos>
    <div style="height: 100vh; display:flex; align-items:center; justify-content:center; background-color: #f1f5f9;">
        <div class="card" style="width: 400px; padding: 2rem; text-align:center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 12px; background: white;">
            
            <div style="margin-bottom: 2rem;">
                <h1 style="color: var(--primary); font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Cofre Trancado 🔒</h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Para iniciar operações de venda, você deve assinar a responsabilidade deste turno.</p>
            </div>

            <form action="{{ route('sales.pos.open') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1.5rem; text-align: left;">
                    <label style="font-weight: 600; font-size: 0.85rem; color: #475569; display:block; margin-bottom: 0.35rem;">Seu PIN (Operador):</label>
                    <input type="password" name="pin" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1.1rem; text-align: center; letter-spacing: 0.25rem;" placeholder="****">
                </div>

                <div style="margin-bottom: 2rem; text-align: left;">
                    <label style="font-weight: 600; font-size: 0.85rem; color: #475569; display:block; margin-bottom: 0.35rem;">Fundo de Troco (R$):</label>
                    <input type="text" name="initial_cash" value="0,00" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1.1rem; text-align: center;">
                    <small style="color: #64748b; font-size:0.75rem; display:block; margin-top:0.25rem;">Valor físico de moedas e notas já deixadas para troco na sua gaveta.</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    🟢 ABRIR TURNO DE CAIXA
                </button>
            </form>
        </div>
    </div>
</x-layouts.pos>
