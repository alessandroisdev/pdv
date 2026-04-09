<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();

        // 1. Filtro por Data
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // 2. Filtro por Evento (Criado, Atualizado, Deletado)
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // 3. Filtro por Modelo Afetado
        if ($request->filled('type')) {
            $query->where('auditable_type', $request->type);
        }

        // 4. Busca Livres (Usuário, IP ou Dados JSON)
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function($q) use ($term) {
                $q->where('ip_address', 'like', $term)
                  ->orWhere('old_values', 'like', $term)
                  ->orWhere('new_values', 'like', $term)
                  ->orWhereHas('user', function($u) use ($term) {
                      $u->where('name', 'like', $term);
                  });
            });
        }

        $audits = $query->paginate(30)->appends($request->query());

        // Metadados para popular os selects de filtro dinamicamente
        $eventTypes = Audit::select('event')->distinct()->pluck('event');
        $models = Audit::select('auditable_type')->distinct()->pluck('auditable_type');

        return view('accesscontrol::audit.index', compact('audits', 'eventTypes', 'models'));
    }
}
