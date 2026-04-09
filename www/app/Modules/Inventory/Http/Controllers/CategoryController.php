<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Modules\Inventory\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('inventory::categories.index', compact('categories'));
    }

    public function datatable(Request $request)
    {
        $query = Category::with('parent')->select('categories.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query,
                $request,
                ['name'],
                function ($category) {
                    $parent = $category->parent ? "<span style='background:#e0f2fe; color:#0284c7; padding:0.25rem 0.5rem; border-radius:0.25rem; font-size:0.75rem; font-weight:bold;'>{$category->parent->name}</span>" : "<span style='color:#94a3b8; font-style:italic; font-size:0.85rem;'>Categoria Raiz Principal</span>";
                    
                    $btnEdit = "<button onclick=\"editCat({$category->id}, '".addslashes($category->name)."', '{$category->parent_id}')\" class=\"btn btn-sm btn-outline\" style=\"border-color: #cbd5e1; color: #475569;\">Editar</button>";
                    
                    $btnDel = "<form action='".route('inventory.categories.destroy', $category->id)."' method='POST' onsubmit=\"return confirm('Tem certeza? Isso pode afetar o cadastro de produtos.')\" style='display:inline;'><input type='hidden' name='_token' value='".csrf_token()."'><input type='hidden' name='_method' value='DELETE'><button type='submit' class='btn btn-sm' style='background: #fee2e2; color: #ef4444; border: 1px solid #fecaca;'>Excluir</button></form>";

                    return [
                        'parent_html' => $parent,
                        'acoes' => "<div style='display:flex; justify-content:flex-end; gap:0.5rem;'>{$btnEdit}{$btnDel}</div>"
                    ];
                }
            )
        );
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
