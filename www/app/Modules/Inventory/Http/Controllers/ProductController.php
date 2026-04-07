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
        $products = Product::with('category')->paginate(15);
        return view('inventory::products.index', compact('products'));
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
