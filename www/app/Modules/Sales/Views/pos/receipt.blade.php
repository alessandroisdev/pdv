<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante #{{ $sale->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto Mono', monospace;
            background-color: #f1f5f9;
            color: #000;
            display: flex;
            justify-content: center;
            padding: 2rem;
        }

        .receipt-container {
            width: 300px;
            background: #fff;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .text-sm { font-size: 0.85rem; }
        .text-xs { font-size: 0.75rem; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 0.75rem 0;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
        }

        /* Thermal specific margins for real printer */
        @media print {
            body { 
                background: #fff; 
                padding: 0; 
                display: block; 
            }
            .receipt-container { 
                width: 79mm; /* Exatamente 80mm com margem fina */
                padding: 0;
                box-shadow: none; 
            }
            /* Esconde botões caso existam botões no popup */
            .no-print { display: none !important; }
        }

        .nfce-block {
            padding: 1rem 0;
            margin-top: 1rem;
            border: 1px solid #000;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <!-- CABEÇALHO -->
        <div class="text-center">
            <h2 class="font-bold">CURITIBA SERVIÇOS</h2>
            <p class="text-sm">Av. Marechal Floriano Peixoto, 123<br>Centro - Curitiba/PR</p>
            <p class="text-sm border-b">CNPJ: 00.000.000/0001-00</p>
        </div>

        <div class="divider"></div>

        <div class="text-center font-bold">
            @if(config('sales.enable_nfce', false))
                <p>NFC-E - Documento Auxiliar da<br>Nota Fiscal Eletrônica</p>
            @else
                <p>DOCUMENTO NÃO FISCAL<br>*** FECHAMENTO INTERNO ***</p>
            @endif
        </div>

        <div class="divider"></div>

        <!-- DADOS DA VENDA -->
        <p class="text-xs text-center">
            Extrato N. <b>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</b><br>
            Emissão: {{ $sale->created_at->format('d/m/Y H:i:s') }}<br>
            Terminal/Caixa ID: {{ $sale->cash_register_id }}
        </p>

        <div class="divider"></div>

        <!-- ITENS -->
        <div class="text-xs">
            <div class="flex-between font-bold" style="margin-bottom: 0.5rem;">
                <span>DESCRIÇÃO</span>
                <span>TOTAL</span>
            </div>
            
            @foreach($sale->items as $item)
            <div style="margin-bottom: 0.5rem;">
                <div class="font-bold">{{ str_pad($item->product->id, 4, '0', STR_PAD_LEFT) }} - {{ \Illuminate\Support\Str::limit($item->product->name, 20) }}</div>
                <div class="flex-between">
                    <span>{{ $item->quantity }} x R$ {{ number_format($item->unit_price_cents / 100, 2, ',', '.') }}</span>
                    <span>R$ {{ number_format(($item->quantity * $item->unit_price_cents) / 100, 2, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- TOTAL -->
        <div class="flex-between font-bold" style="font-size: 1.15rem;">
            <span>TOTAL R$</span>
            <span>{{ number_format($sale->total_cents / 100, 2, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <!-- TRIBUTOS / NFC-e -->
        @if(config('sales.enable_nfce', false))
            <div class="nfce-block text-xs">
                <div>Consulte pela Chave de Acesso em:</div>
                <div style="word-wrap: break-word; font-weight:700; margin: 0.5rem 0;">4126 0400 0000 0000 1000 6500 0000 000{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }} 1234</div>
                
                <div style="margin-top: 0.5rem; text-align: center;">
                    <!-- Simulador de QR Code com CSS Grid pra visual layout print -->
                    <div style="display:inline-block; width: 100px; height: 100px; background: url('https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=nfce{{$sale->id}}') center/cover;"></div>
                </div>
                <div>Protocolo de Autorização:<br>1412000{{ $sale->id }}{{ rand(1000, 9999) }}</div>
            </div>
        @else
            <div class="text-center text-xs" style="margin: 1rem 0;">
                Obrigado pela preferência!<br>
                Software Desenvolvido por NORTE.DEV
            </div>
        @endif

        <div class="text-center no-print" style="margin-top: 2rem;">
            <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ IMPRIMIR AGORA</button>
        </div>
    </div>

</body>
</html>
