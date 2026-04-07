<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lote de Etiquetas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background-color: #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            gap: 1rem;
        }

        .label-page {
            width: {{ $template->width_mm }}mm;
            height: {{ $template->height_mm }}mm;
            background: #fff;
            border: 1px dotted #ccc;
            overflow: hidden;
            position: relative;
            /* Flex layout center default for rendering */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media print {
            body { 
                background: #fff; 
                padding: 0;
                display: block; 
            }
            .label-page { 
                border: none;
                page-break-after: always;
            }
            .no-print { display: none !important; }
        }

        .btn-print {
            background: #10b981;
            color: #fff;
            padding: 10px 30px;
            font-size: 1.2rem;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <button class="btn-print no-print" onclick="window.print()">🖨️ Descarregar para Impressora</button>

    @foreach($labelsToPrint as $product)
        <div class="label-page">
            <!-- Dynamic Template Engine Render -->
            {!! str_replace(
                ['{{name}}', '{{price}}', '{{barcode}}'], 
                [   
                    \Illuminate\Support\Str::limit($product->name, 25), 
                    number_format($product->price->getCents() / 100, 2, ',', '.'), 
                    $product->barcode ?? str_pad($product->id, 8, '0', STR_PAD_LEFT)
                ], 
                $template->layout_html) 
            !!}
        </div>
    @endforeach

    @if(count($labelsToPrint) == 0)
        <div style="font-family: sans-serif; background: #fff; padding: 2rem; text-align: center;">Nenhuma etiqueta solicitada neste lote.</div>
    @endif

</body>
</html>
