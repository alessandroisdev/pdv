@php
    $prefixPath = request()->is('terminal*') ? '/terminal' : '/vendas/pdv';
@endphp
<x-layouts.pos>
    <div style="height: 100vh; display:flex; align-items:center; justify-content:center; background-color: #0f172a;">
        <div class="card" style="width: 400px; padding: 2rem; text-align:center; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border-radius: 12px; background: rgba(30,41,59,0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
            
            <div style="margin-bottom: 2rem;">
                <h1 style="color: #38bdf8; font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-transform:uppercase; letter-spacing:0.05em;">Terminal Trancado 🔒</h1>
                <p style="color: #94a3b8; font-size: 0.9rem;">Autentique seu PIN de Operador para assumir o front de caixa.</p>
            </div>

            <form action="{{ $prefixPath }}/abrir" method="POST">
                @csrf
                <div style="margin-bottom: 1.5rem; text-align: left;">
                    <label style="font-weight: 600; font-size: 0.85rem; color: #cbd5e1; display:block; margin-bottom: 0.35rem;">Seu PIN (Numérico):</label>
                    <input type="password" name="pin" required style="width: 100%; padding: 0.75rem; border: 1px solid #334155; border-radius: 6px; font-size: 1.1rem; text-align: center; letter-spacing: 0.25rem; background:#1e293b; color:white; outline:none;" placeholder="****">
                </div>

                <div style="margin-bottom: 2rem; text-align: left;">
                    <label style="font-weight: 600; font-size: 0.85rem; color: #cbd5e1; display:block; margin-bottom: 0.35rem;">Fundo de Troco Misto (R$):</label>
                    <input type="text" name="initial_cash" value="0,00" required style="width: 100%; padding: 0.75rem; border: 1px solid #334155; border-radius: 6px; font-size: 1.1rem; text-align: center; background:#1e293b; color:white; outline:none;">
                    <small style="color: #64748b; font-size:0.75rem; display:block; margin-top:0.25rem;">Valor físico de moedas e notas já deixadas na sua gaveta.</small>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; font-weight: bold; background: linear-gradient(135deg, rgba(56,189,248,0.2) 0%, rgba(2,132,199,0.4) 100%); border: 1px solid rgba(56,189,248,0.5); color: #bae6fd; border-radius: 8px;">
                    🟢 ABRIR TURNO
                </button>
            </form>
        </div>
    </div>
</x-layouts.pos>
