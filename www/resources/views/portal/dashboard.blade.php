<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
    </style>
</head>
<body class="text-slate-800">

    <nav class="bg-indigo-600 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <i class="fa fa-briefcase text-2xl"></i>
                    <span class="font-bold text-lg">Meu Portal</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium">{{ $customer->name }}</span>
                    <form action="{{ route('portal.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 px-3 py-1.5 rounded text-sm transition-colors">
                            <i class="fa fa-sign-out-alt"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Visão Geral Financeira</h1>
            <p class="text-slate-500">Acompanhe suas compras, orçamentos e boletos em aberto.</p>
        </div>

        @if(count($installments) == 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                <i class="fa fa-check-circle text-emerald-500 text-5xl mb-4"></i>
                <h2 class="text-xl font-bold text-slate-800 mb-2">Tudo em dia!</h2>
                <p class="text-slate-500">Você não possui faturas ou boletos em aberto no momento.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($installments as $inst)
                    @php
                        $isOverdue = Carbon\Carbon::parse($inst->due_date)->isPast() && !$inst->due_date->isToday();
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border {{ $isOverdue ? 'border-red-300' : 'border-slate-200' }} overflow-hidden flex flex-col">
                        <div class="p-5 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded">FATURA</span>
                                @if($isOverdue)
                                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2.5 py-1 rounded">VENCIDA</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-600 text-xs font-bold px-2.5 py-1 rounded">A VENCER</span>
                                @endif
                            </div>
                            
                            <h3 class="text-lg font-bold text-slate-800">{{ current_currency() }} {{ number_format($inst->amount_cents / 100, 2, ',', '.') }}</h3>
                            <p class="text-sm text-slate-500 mb-4">{{ $inst->description }}</p>
                            
                            <div class="flex items-center gap-2 text-sm {{ $isOverdue ? 'text-red-500 font-bold' : 'text-slate-600' }}">
                                <i class="fa fa-calendar"></i> Vencimento: {{ Carbon\Carbon::parse($inst->due_date)->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 border-t border-slate-100 flex gap-2">
                            <button onclick="requestPix({{ $inst->id }})" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded text-sm text-center transition-colors">
                                <i class="fa fa-qrcode"></i> Pagar via PIX
                            </button>
                            <button onclick="alert('Download do PDF do Boleto estará disponível em breve.')" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-4 rounded text-sm text-center transition-colors">
                                <i class="fa fa-print"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <!-- Modal do PIX -->
    <div id="pix-modal" class="fixed inset-0 z-50 hidden bg-slate-900/70 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 text-center">
            <h3 class="text-xl font-bold text-slate-800 mb-2"><i class="fa fa-qrcode text-emerald-500"></i> Pagamento Instantâneo</h3>
            <p class="text-sm text-slate-500 mb-6" id="pix-instruction">Escaneie o QR Code ou utilize o PIX Copia e Cola para pagar o valor de <strong id="pix-amount">R$ 0,00</strong>.</p>
            
            <div class="bg-slate-100 border border-slate-200 rounded-lg p-4 mb-6 flex justify-center items-center h-48" id="qrcode-container">
                <!-- Simula QR Code visual -->
                <i class="fa fa-qrcode text-8xl text-slate-400" id="qrcode-mock-icon"></i>
                <img id="qrcode-image" class="hidden h-full w-full object-contain" src="" alt="QR Code PIX">
            </div>
            
            <label class="block text-left text-xs font-bold text-slate-700 mb-1">PIX Copia e Cola:</label>
            <div class="flex border border-slate-300 rounded mb-6 overflow-hidden">
                <input type="text" id="pix-payload-input" readonly class="flex-1 px-3 py-2 text-sm text-slate-600 bg-slate-50 focus:outline-none">
                <button onclick="copyPix()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 transition-colors"><i class="fa fa-copy"></i> Copiar</button>
            </div>
            
            <button onclick="document.getElementById('pix-modal').classList.add('hidden')" class="w-full text-slate-500 hover:text-slate-800 font-bold py-2">
                Fechar Janela
            </button>
        </div>
    </div>

    <script>
        function requestPix(id) {
            fetch(`/portal/installments/${id}/pix`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert('Erro: ' + data.error);
                } else {
                    document.getElementById('pix-amount').innerText = 'R$ ' + data.amount_brl;
                    document.getElementById('pix-payload-input').value = data.pix_payload;

                    if (data.gateway_url && data.gateway_url.startsWith('data:image')) {
                         document.getElementById('qrcode-mock-icon').classList.add('hidden');
                         const img = document.getElementById('qrcode-image');
                         img.src = data.gateway_url;
                         img.classList.remove('hidden');
                    }

                    document.getElementById('pix-modal').classList.remove('hidden');
                }
            })
            .catch(err => {
                alert('Ocorreu um erro na comunicação com o Banco/Gateway.');
            });
        }

        function copyPix() {
            var copyText = document.getElementById("pix-payload-input");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            alert("Código PIX copiado para a área de transferência!");
        }
    </script>

</body>
</html>
