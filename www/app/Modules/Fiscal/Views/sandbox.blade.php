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
            
            <div class="mt-6 flex gap-2">
                <button class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-white font-bold" disabled>
                    <i class="fa fa-paper-plane"></i> Testar Conexão SOAP SEFAZ (Requer NFePHP instalado)
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
