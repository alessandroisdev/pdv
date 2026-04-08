<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        return redirect()->back()->with('warning', 'O gerenciamento detalhado de categorias será disponibilizado na próxima atualização.');
    }
}
