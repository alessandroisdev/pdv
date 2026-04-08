<x-layouts.app>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Motor Tributário - Sandbox</h2>
        <p class="text-slate-500">Laboratório de Testes de Configuração NFC-e / NFePHP</p>
    </div>

    <div class="card bg-white shadow-sm border border-slate-200">
        <div class="card-header bg-slate-50 border-b border-slate-100 flex justify-between items-center p-4">
            <h3 class="font-bold text-slate-700 m-0"><i class="fa fa-vial text-indigo-500"></i> Status do Motor de Geração</h3>
            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full uppercase">{{ $sandboxData['status'] }}</span>
        </div>
        <div class="card-body p-6">
            <p class="mb-4 text-emerald-600 font-bold"><i class="fa fa-check-circle"></i> {{ $sandboxData['message'] }}</p>

            <h4 class="font-bold text-slate-700 mb-2">Payload JSON (NFePHP Tools Constructor)</h4>
            <div class="bg-slate-900 rounded p-4 overflow-x-auto">
                <pre class="text-emerald-400 text-sm font-mono m-0"><code>{{ json_encode($sandboxData['config_generated'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
            
            <div class="mt-6 flex flex-col gap-4">
                <div class="flex gap-2">
                    <button id="btn-ping" onclick="pingSefaz()" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded">
                        <i class="fa fa-plug"></i> Testar Conexão SOAP SEFAZ
                    </button>
                </div>
                <div id="ping-result" class="hidden p-4 rounded-lg text-sm font-mono border">
                </div>
            </div>
        </div>
    </div>

    <script>
        function pingSefaz() {
            const btn = document.getElementById('btn-ping');
            const resultBox = document.getElementById('ping-result');
            
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Conectando...';
            btn.disabled = true;
            resultBox.classList.add('hidden');
            resultBox.innerHTML = '';

            fetch('{{ route("fiscal.sandbox.ping") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = '<i class="fa fa-plug"></i> Testar Conexão SOAP SEFAZ';
                btn.disabled = false;
                resultBox.classList.remove('hidden');

                if(data.success) {
                    resultBox.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
                    resultBox.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                    resultBox.innerHTML = `<strong>SUCESSO!</strong><br>Status: ${data.status_code}<br>Motivo: ${data.reason}<br>Latência: ${data.latency} Segundos`;
                } else {
                    resultBox.classList.remove('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                    resultBox.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                    resultBox.innerHTML = `<strong>FALHA:</strong><br>${data.error}`;
                }
            })
            .catch(err => {
                btn.innerHTML = '<i class="fa fa-plug"></i> Testar Conexão SOAP SEFAZ';
                btn.disabled = false;
                resultBox.classList.remove('hidden');
                resultBox.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                resultBox.innerHTML = `<strong>ERRO FATAL:</strong> Não foi possível comunicar com o servidor interno.`;
            });
        }
    </script>
</x-layouts.app>
