<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KdsController extends Controller
{
    /**
     * Show the Kitchen Display System Interface
     */
    public function index()
    {
        $branchId = Auth::user()->branch_id ?? 1;

        // Renderiza o Blade com JS Echo
        return view('kds.index', compact('branchId'));
    }
}
