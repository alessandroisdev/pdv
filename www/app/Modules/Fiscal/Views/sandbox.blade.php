<x-layouts.app>
    <div class="p-6">
        <div class="mb-4 border-b border-slate-200 pb-4" style="margin-bottom: 2rem;">
            <h2 class="text-primary fw-bold" style="font-size: 1.75rem;">Motor Tributário - Sandbox</h2>
            <p class="text-light" style="margin-top: 0.25rem;">Laboratório de Testes de Configuração NFC-e / NFePHP.</p>
        </div>

        <div class="card bg-white border-0 shadow-sm" style="border-radius: 0.75rem; overflow: hidden;">
            <div style="background: #f8fafc; border-bottom: 1px solid #f1f5f9; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.125rem; font-weight: bold; color: #1e293b; margin: 0;">
                    <i class="fa fa-vial" style="color: #6366f1;"></i> Status do Motor de Geração
                </h3>
                <span style="background: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">
                    {{ $sandboxData['status'] }}
                </span>
            </div>
            
            <div style="padding: 1.5rem;">
                <p style="margin-bottom: 1.5rem; color: #059669; font-weight: bold; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa fa-check-circle"></i> {{ $sandboxData['message'] }}
                </p>

                <h4 style="font-weight: bold; color: #334155; margin-bottom: 0.75rem; font-size: 0.875rem;">Payload JSON (NFePHP Tools Constructor)</h4>
                <div style="background: #0f172a; padding: 1rem; border-radius: 0.5rem; overflow-x: auto;">
                    <pre style="color: #34d399; font-size: 0.875rem; font-family: monospace; margin: 0;"><code>{{ json_encode($sandboxData['config_generated'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                
                <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <button id="btn-ping" onclick="pingSefaz()" class="btn shadow" style="background: #4f46e5; border: none; color: white; padding: 0.75rem 1.5rem; font-weight: bold; border-radius: 0.5rem; cursor: pointer; transition: background 0.2s;">
                            <i class="fa fa-plug" style="margin-right: 0.5rem;"></i> Testar Conexão SOAP SEFAZ
                        </button>
                    </div>
                    <div id="ping-result" style="display: none; padding: 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-family: monospace; border: 1px solid #e2e8f0;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function pingSefaz() {
            const btn = document.getElementById('btn-ping');
            const resultBox = document.getElementById('ping-result');
            
            btn.innerHTML = '<i class="fa fa-spinner fa-spin" style="margin-right: 0.5rem;"></i> Conectando...';
            btn.disabled = true;
            btn.style.opacity = '0.7';
            
            resultBox.style.display = 'none';
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
                btn.innerHTML = '<i class="fa fa-plug" style="margin-right: 0.5rem;"></i> Testar Conexão SOAP SEFAZ';
                btn.disabled = false;
                btn.style.opacity = '1';
                resultBox.style.display = 'block';

                if(data.success) {
                    resultBox.style.background = '#ecfdf5';
                    resultBox.style.borderColor = '#a7f3d0';
                    resultBox.style.color = '#047857';
                    resultBox.innerHTML = `<strong>SUCESSO!</strong><br>Status: ${data.status_code}<br>Motivo: ${data.reason}<br>Latência: ${data.latency} Segundos`;
                } else {
                    resultBox.style.background = '#fef2f2';
                    resultBox.style.borderColor = '#fecaca';
                    resultBox.style.color = '#b91c1c';
                    resultBox.innerHTML = `<strong>FALHA:</strong><br>${data.error}`;
                }
            })
            .catch(err => {
                btn.innerHTML = '<i class="fa fa-plug" style="margin-right: 0.5rem;"></i> Testar Conexão SOAP SEFAZ';
                btn.disabled = false;
                btn.style.opacity = '1';
                
                resultBox.style.display = 'block';
                resultBox.style.background = '#fef2f2';
                resultBox.style.borderColor = '#fecaca';
                resultBox.style.color = '#b91c1c';
                resultBox.innerHTML = `<strong>ERRO FATAL:</strong> Não foi possível comunicar com o servidor interno.`;
            });
        }
    </script>
</x-layouts.app>
