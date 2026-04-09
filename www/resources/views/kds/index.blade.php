<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KDS - Cozinha / Expedição</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite: Carrega Echo e Pusher via Reverb compilado -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #f8fafc; }
        .order-card { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        /* Animação de Pulsar Vermelho Urgente */
        .urgent-pulse { animation: pulseRed 2s infinite; }
        @keyframes pulseRed { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="bg-slate-900 border-b border-slate-800 p-4 flex justify-between items-center shadow-lg">
        <div class="flex items-center gap-3">
            <i class="fa fa-fire text-amber-500 text-3xl"></i>
            <div>
                <h1 class="text-xl font-bold tracking-wider uppercase text-slate-100">Painel de Cozinha (KDS)</h1>
                <p class="text-xs text-slate-400">FILIAL #{{ $branchId }} • EM TEMPO REAL</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span id="socket-status" class="bg-amber-500/20 text-amber-500 border border-amber-500/50 px-3 py-1 rounded text-sm font-bold flex items-center gap-2">
                <i class="fa fa-spinner fa-spin"></i> CONECTANDO REVERB...
            </span>
            <button onclick="document.documentElement.requestFullscreen()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-1.5 rounded transition">
                <i class="fa fa-expand"></i> Tela Cheia
            </button>
        </div>
    </header>

    <!-- Board de Tickets -->
    <main class="flex-1 p-6 overflow-x-auto">
        <div id="kds-board" class="flex gap-4 h-full items-start">
            
            <!-- Os cards entrarão aqui dinamicamente -->
            <div class="bg-slate-800/50 border border-slate-700 p-8 rounded-xl flex items-center justify-center min-w-[300px] h-full" id="empty-state">
                <div class="text-center text-slate-500">
                    <i class="fa fa-mug-hot text-5xl mb-4 opacity-50"></i>
                    <p class="font-bold text-lg">Cozinha Ociosa</p>
                    <p class="text-sm">Aguardando novos pedidos via Omnichannel ou Caixa...</p>
                </div>
            </div>

        </div>
    </main>

    <!-- Audio Player Oculto para Beep -->
    <audio id="bell-sound" src="https://cdn.pixabay.com/download/audio/2021/08/04/audio_0625c1539c.mp3?filename=service-bell-ring-14610.mp3" preload="auto"></audio>

    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            const branchId = {{ $branchId }};
            const board = document.getElementById('kds-board');
            const emptyState = document.getElementById('empty-state');
            const statusBadge = document.getElementById('socket-status');
            const bell = document.getElementById('bell-sound');

            // Exibir Online se WebSocket bater
            setTimeout(() => {
                if (window.Echo) {
                    statusBadge.className = "bg-emerald-500/20 text-emerald-400 border border-emerald-500/50 px-3 py-1 rounded text-sm font-bold flex items-center gap-2";
                    statusBadge.innerHTML = '<i class="fa fa-link"></i> SOCKET ONLINE';
                }
            }, 1500);

            // Ouvinte de Eventos Privados (Requires Auth Handshake)
            if (window.Echo) {
                window.Echo.private(`kds.branch.${branchId}`)
                    .listen('.new.order', (e) => {
                        console.log("NOVO PEDIDO CHEGOU:", e);
                        
                        // Remover Empty State se existir
                        if (emptyState) emptyState.style.display = 'none';

                        // Tocar Sino de Serviço
                        bell.play().catch(e => console.log('Bloqueio do navegador pra som autoplay.'));

                        // Injetar Card Vermelho Pulsante
                        const p = e.salePayload;
                        const card = document.createElement('div');
                        card.className = "bg-red-600 rounded-xl shadow-2xl p-4 min-w-[320px] max-w-[320px] order-card flex flex-col urgent-pulse";
                        card.innerHTML = `
                            <div class="flex justify-between items-center mb-4 border-b border-red-500/50 pb-2">
                                <span class="bg-red-800 text-white text-xs font-black px-2 py-1 rounded">MESA/CLI: ${p.customer}</span>
                                <span class="font-mono font-bold text-black bg-yellow-400 px-2 rounded">${p.timestamp}</span>
                            </div>
                            <div class="flex-1 bg-red-50 rounded p-3 text-red-900 shadow-inner">
                                <h3 class="font-black text-xl mb-1 mt-1 border-b border-red-200 pb-2">PEDIDO #${p.id}</h3>
                                <p class="text-sm font-bold mb-3 text-red-700">Origem: Autoatendimento PWA</p>
                                
                                <ul class="space-y-2 mt-4 text-sm font-bold">
                                    <li class="flex items-center gap-2 border-b border-red-200 pb-1"><span class="bg-red-200 py-0.5 px-2 rounded text-red-800 font-mono">1x</span> Produto Omnichannel Generico</li>
                                </ul>
                            </div>
                            <button onclick="this.parentElement.remove()" class="mt-4 bg-red-800 hover:bg-black text-white font-bold py-3 rounded uppercase text-sm tracking-widest transition shadow-lg">
                                <i class="fa fa-check-circle mr-1"></i> Despachar
                            </button>
                        `;
                        
                        // Adicionar lado a lado tipo Kanban
                        board.prepend(card);
                    });
            }
        });
    </script>
</body>
</html>
