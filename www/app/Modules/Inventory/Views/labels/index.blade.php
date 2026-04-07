<x-layouts.app>
    <div class="card">
        <div class="card-header border-b p-4">
            <h3 class="fw-bold text-primary">Motor de Etiquetas</h3>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('inventory.labels.generate') }}" method="POST" target="_blank">
                @csrf
                <div class="mb-4">
                    <label class="font-bold mb-2 block">Formato Base</label>
                    <select name="template_id" class="form-control" style="width: 100%; padding: 10px;">
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->width_mm }}x{{ $template->height_mm }}mm)</option>
                        @endforeach
                    </select>
                </div>

                <h4 class="font-bold mb-3 mt-6">Produtos para Impressão</h4>
                <div style="max-height: 400px; overflow-y: auto; border: 1px solid #e2e8f0; padding: 10px;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 10px;">Cód.</th>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th style="width: 150px;">Qtd de Etiquetas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px;">{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $product->name }}</td>
                                <td>R$ {{ number_format($product->price->getCents() / 100, 2, ',', '.') }}</td>
                                <td>
                                    <input type="number" name="products[{{ $product->id }}]" min="0" value="0" class="form-control" style="width: 80px; padding: 5px;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 text-right">
                    <button type="submit" class="btn btn-primary">
                        🖨️ Processar Lote e Imprimir
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
