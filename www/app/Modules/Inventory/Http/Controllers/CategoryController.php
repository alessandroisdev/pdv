<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Inventory\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('name')->get();
        return view('inventory::categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        Category::create($data);
        return redirect()->route('inventory.categories.index')->with('success', 'Categoria cadastrada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        if ($data['parent_id'] == $category->id) {
            return redirect()->back()->with('error', 'Uma categoria não pode ser pai dela mesma.');
        }

        $category->update($data);
        return redirect()->route('inventory.categories.index')->with('success', 'Categoria atualizada!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->children()->count() > 0) {
            return redirect()->back()->with('error', 'Não é possível excluir esta categoria pois ela possui sub-categorias.');
        }

        if (\App\Modules\Inventory\Models\Product::where('category_id', $category->id)->exists()) {
             return redirect()->back()->with('error', 'Não é possível excluir: Existem produtos vinculados a esta categoria.');
        }

        $category->delete();
        return redirect()->route('inventory.categories.index')->with('success', 'Categoria excluída com sucesso!');
    }
}
