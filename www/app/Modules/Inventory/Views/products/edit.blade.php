<x-layouts.app>
    <x-slot:title>Editar Produto | Estoque</x-slot:title>

    <div class="mb-4">
        <a href="{{ route('inventory.products.index') }}" class="text-light fw-semibold" style="text-decoration: none; font-size: 0.85rem;">&larr; Voltar para a lista</a>
        <h1 class="text-primary fw-bold mt-4" style="font-size: 1.75rem;">Editar Produto</h1>
    </div>

    <x-ui.card>
        <x-slot:header>Modifique os dados técnicos</x-slot:header>

        <form action="{{ route('inventory.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Nome -->
                <div>
                    <label class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">Nome do Produto <span class="text-contrast">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                    @error('name') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Categoria -->
                <div>
                    <label class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">Categoria</label>
                    <select name="category_id" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; background: white; outline: none;">
                        <option value="">Nenhuma Categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- SKU -->
                <div>
                    <label class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">SKU (Código Interno)</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;" placeholder="PROD-0001">
                    @error('sku') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Código Barras -->
                <div>
                    <label class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">Cód. de Barras (EAN)</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                    @error('barcode') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Custo -->
                <div>
                    <label class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">Preço de Custo (R$) <span class="text-contrast">*</span></label>
                    <input type="text" name="cost_price" value="{{ old('cost_price', number_format($product->cost_price->getReais(), 2, ',', '')) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;" placeholder="ex: 15,50">
                    @error('cost_price') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <!-- Venda -->
                <div>
                    <label class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">Preço de Venda (R$) <span class="text-contrast">*</span></label>
                    <input type="text" name="sale_price" value="{{ old('sale_price', number_format($product->sale_price->getReais(), 2, ',', '')) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;" placeholder="ex: 35,90">
                    @error('sale_price') <span style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Descrição -->
            <div style="margin-bottom: 2rem;">
                <label class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">Descrição do Produto</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; resize: vertical;">{{ old('description', $product->description) }}</textarea>
            </div>

            <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                <a href="{{ route('inventory.products.index') }}" class="btn btn-outline" style="margin-right: 1rem;">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem;">Atualizar Produto</button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
