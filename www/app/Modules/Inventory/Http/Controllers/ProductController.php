<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Product;
use App\Modules\Inventory\Models\Category;
use App\Modules\Inventory\Http\Requests\ProductStoreRequest;
use App\Modules\Core\ValueObjects\Money;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // View Render: As listagens agora são carregadas client-side via DataTable
        return view('inventory::products.index');
    }

    public function datatable(Request $request)
    {
        $query = Product::with('category')->select('products.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query,
                $request,
                ['name', 'sku', 'barcode'], // searchableColuns
                function ($product) {
                    // Formatter Hook para injeção de Colunas Virtuais
                    $catName = $product->category ? $product->category->name : 'Sem Categoria';
                    $stockClass = $product->current_stock > 10 ? 'background-color: #d1fae5; color: #047857;' : 'background-color: #ffe4e6; color: #be123c;';
                    $stockBadge = "<span style='display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; {$stockClass}'>{$product->current_stock} Und</span>";
                    
                    $costClone = clone $product->cost_price;
                    $saleClone = clone $product->sale_price;

                    $btnEstoque = "<a href='".route('inventory.products.stock', $product->id)."' class='btn btn-outline' style='padding: 0.25rem 0.5rem; font-size: 0.75rem; text-decoration: none; border-color: #10b981; color: #047857;'><i class='fa fa-boxes'></i> Estoque</a>";
                    $btnEdit = "<a href='".route('inventory.products.edit', $product->id)."' class='btn btn-outline' style='padding: 0.25rem 0.5rem; font-size: 0.75rem; text-decoration: none;'>Editar</a>";

                    return [
                        'category_name' => $catName,
                        'virtual_stock' => $stockBadge,
                        'cost_price_formatted' => (string) $costClone,
                        'sale_price_formatted' => (string) $saleClone,
                        'acoes' => "<div style='display:flex; justify-content:flex-end; gap:0.5rem;'>{$btnEstoque}{$btnEdit}</div>"
                    ];
                }
            )
        );
    }

    public function create()
    {
        $categories = Category::all();
        return view('inventory::products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        $costCents = $this->parseCurrencyToCents($data['cost_price']);
        $saleCents = $this->parseCurrencyToCents($data['sale_price']);

        $product = new Product();
        $product->fill([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'barcode' => $data['barcode'],
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?: null,
        ]);
        
        $product->cost_price = new Money($costCents);
        $product->sale_price = new Money($saleCents);
        
        $product->save();

        return redirect()->route('inventory.products.index')->with('success', 'Produto cadastrado com sucesso!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('inventory::products.edit', compact('product', 'categories'));
    }

    public function update(ProductStoreRequest $request, Product $product)
    {
        $data = $request->validated();

        $costCents = $this->parseCurrencyToCents($data['cost_price']);
        $saleCents = $this->parseCurrencyToCents($data['sale_price']);

        $product->fill([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'barcode' => $data['barcode'],
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?: null,
        ]);
        
        $product->cost_price = new Money($costCents);
        $product->sale_price = new Money($saleCents);
        
        $product->save();

        return redirect()->route('inventory.products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Helper to bypass float errors converting user string (1.200,50) to integer cents (120050)
     */
    private function parseCurrencyToCents(string $value): int
    {
        $clean = str_replace('.', '', $value);
        $clean = str_replace(',', '.', $clean);
        return (int) round(((float) $clean) * 100);
    }
}
