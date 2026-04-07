<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Impressão de Crachá | {{ $employee->name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #e2e8f0;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Standard CR80 PVC Card Dimensions */
        .badge-card {
            width: 53.98mm;
            height: 85.6mm;
            background: #ffffff;
            position: relative;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-radius: 4px;
        }

        .badge-front, .badge-back {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 4mm;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            background: white;
            text-align: center;
        }

        .badge-header {
            width: 100%;
            background: #c0904d;
            color: #fff;
            padding: 2mm 0;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2mm;
        }

        .photo-placeholder {
            width: 25mm;
            height: 28mm;
            background-color: #f1f5f9;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            margin-bottom: 3mm;
        }

        .employee-name {
            font-size: 9pt;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.1;
            margin-bottom: 1mm;
        }

        .employee-role {
            font-size: 7pt;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 2mm;
        }

        .barcode-container {
            margin-top: auto;
            width: 100%;
            text-align: center;
        }

        .barcode-container svg {
            width: 100%;
            height: 8mm;
        }

        .barcode-text {
            font-size: 6pt;
            font-family: monospace;
            color: #475569;
            letter-spacing: 2px;
            margin-top: 1mm;
        }

        /* Back Card */
        .qr-container {
            margin: auto;
            width: 32mm;
            height: 32mm;
            background: #fff;
        }

        .warning-text {
            font-size: 5pt;
            color: #64748b;
            margin-top: 2mm;
            text-align: justify;
            line-height: 1.2;
        }

        .print-canvas {
            display: flex;
            gap: 10mm;
        }

        /* Impressão */
        @media print {
            body {
                background: none;
                display: block;
                height: auto;
            }
            .print-canvas {
                margin: 0;
                padding: 0;
                display: flex;
                gap: 5mm;
            }
            .badge-card {
                box-shadow: none;
                /* Removes the screen border, so the printer cuts properly without a physical border */
                border-radius: 0;
            }
        }

        .no-print {
            padding: 10px;
            background: #1e293b;
            color: white;
            text-align: center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding:8px 16px; background:#10b981; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">
            Imprimir Crachá em PVC / Cartão Térmico
        </button>
        <button onclick="window.history.back()" style="padding:8px 16px; background:#64748b; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer; margin-left:10px;">
            Voltar
        </button>
        <p style="font-size: 0.8rem; margin-top:5px; color:#cbd5e1;">Ajuste sua impressora para tamanho de Papel 54mm x 86mm (CR80) com Margens Zero.</p>
    </div>

    <div class="print-canvas">
        <!-- FRONT CARD -->
        <div class="badge-card">
            <div class="badge-header">Gestão PDV</div>
            <div class="badge-front">
                <div class="photo-placeholder">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div class="employee-name">{{ mb_strtoupper($employee->name) }}</div>
                <div class="employee-role">
                    @if($employee->level === 'SUPERVISOR')
                        SUPERVISOR DE CAIXA
                    @else
                        CAIXA / OPERADOR
                    @endif
                </div>

                <div class="barcode-container">
                    {!! $barcodeSVG !!}
                    <div class="barcode-text">PIN: {{ $employee->pin }}</div>
                </div>
            </div>
        </div>

        <!-- BACK CARD -->
        <div class="badge-card">
            <div class="badge-back" style="justify-content: center; background: #f8fafc;">
                <div style="font-weight: bold; font-size: 7pt; margin-bottom: 2mm; color:#475569;">CHAVE BLINDADA</div>
                <div class="qr-container">
                    <!-- simple-qrcode integration -->
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($qrHash) !!}
                </div>
                <div style="margin-top:4mm; font-size: 6pt; color: #1e293b; font-weight: bold;">MATRÍCULA ALFA: #{{ str_pad($employee->id, 5, '0', STR_PAD_LEFT) }}</div>
                <p class="warning-text">
                    Este crachá é de uso pessoal e intransferível. O QR Code acima encontra-se cifrado em AES-256 e possui permissões diretas no servidor.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
