<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Terminal PDV | Caixa Livre</title>
    <!-- Entrada Modular Exclusiva do POS (Sem estilos do painel Admin) -->
    @vite(['resources/scss/pos.scss', 'resources/ts/pos.ts'])
</head>
<body>
    <div class="pos-layout">
        <header class="pos-header">
            <div class="pos-brand">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 0.5rem;"><path d="M4 7v10l8 4 8-4V7M4 7l8-4 8 4M4 7l8 4 8-4"></path></svg>
                <span>Gestão</span>PDV
            </div>
            
            <div style="display: flex; gap: 2rem; align-items: center;">
                <div style="font-size: 1.15rem; font-weight: 500; font-family: monospace;" id="pos-clock">--:--:--</div>
                
                <div style="display: flex; gap: 1rem; align-items: center; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 1rem;">
                    <div style="text-align: right; line-height: 1.2;">
                        <strong style="display:block; font-size: 0.95rem;">{{ Auth::user()->name ?? 'Operador' }}</strong>
                        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.7);">Caixa ABERTO</span>
                    </div>
                    
                    <form method="POST" action="/logout" style="margin:0;">
                        @csrf
                        <button type="button" data-confirm="Tem certeza que deseja sair do sistema? Isso não fechará o Turno contabilmente se houver dinheiro na gaveta." class="btn-pos" style="background: transparent; border: 1px solid rgba(255,255,255,0.3); padding: 0.5rem 1rem;">SAIR</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="pos-body">
            {{ $slot }}
        </main>
    </div>

    <!-- Feedback Listener Nativo (Toast e Confirms configurados no pos.ts) -->
    <script>
        setInterval(() => {
            const clock = document.getElementById('pos-clock');
            if(clock) {
                const now = new Date();
                clock.textContent = now.toLocaleTimeString('pt-BR');
            }
        }, 1000);

        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                if(window.toast) { window.toast.fire({ icon: 'success', title: '{{ session('success') }}' }); } 
                else { alert("✅ SUCESSO:\n{{ session('success') }}"); }
            @endif
            @if(session('error'))
                if(window.toast) { window.toast.fire({ icon: 'error', title: '{{ session('error') }}' }); }
                else { alert("❌ ERRO:\n{{ session('error') }}"); }
            @endif
        });
    </script>
</body>
</html>
