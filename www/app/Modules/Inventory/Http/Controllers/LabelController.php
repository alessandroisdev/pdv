<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\LabelTemplate;
use App\Modules\Inventory\Models\Product;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        // Se a base estiver vazia, geramos o layout Base Padrão de Gôndola (Supermercado)
        if (LabelTemplate::count() === 0) {
            LabelTemplate::create([
                'name' => 'Etiqueta de Gôndola (Termal Padrao 110x40mm)',
                'width_mm' => 110,
                'height_mm' => 40,
                'layout_html' => '<div style="font-family:sans-serif; text-align:center;">
                    <div style="font-size:1.5em; font-weight:bold;">{{name}}</div>
                    <div style="font-size:2em; font-weight:800; color:#000;">R$ {{price}}</div>
                    <div style="font-size:0.8em; margin-top:5px;">Cod: {{barcode}}</div>
                 </div>'
            ]);
        }

        $templates = LabelTemplate::all();
        $products = Product::where('status', true)->get();

        return view('inventory::labels.index', compact('templates', 'products'));
    }

    public function generate(Request $request)
    {
        $template = LabelTemplate::findOrFail($request->input('template_id'));
        $productKeys = $request->input('products', []); // [ product_id => qty ]
        
        $labelsToPrint = [];

        foreach ($productKeys as $pId => $qty) {
            if ($qty > 0) {
                // Instanciar o produto e explodir a quantidade requisitada
                $product = Product::find($pId);
                if ($product) {
                    for ($i = 0; $i < $qty; $i++) {
                        $labelsToPrint[] = $product;
                    }
                }
            }
        }

        return view('inventory::labels.print', compact('template', 'labelsToPrint'));
    }
}
