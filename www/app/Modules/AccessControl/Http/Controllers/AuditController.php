<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // Metadados para popular os selects de filtro dinamicamente
        $eventTypes = Audit::select('event')->distinct()->pluck('event');
        $models = Audit::select('auditable_type')->distinct()->pluck('auditable_type');

        return view('accesscontrol::audit.index', compact('eventTypes', 'models'));
    }

    public function datatable(Request $request)
    {
        $query = Audit::with('user')->select('audits.*');

        // Filtros avançados recebidos por Query Params na inicialização do Datatable
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('type')) {
            $query->where('auditable_type', $request->type);
        }

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['ip_address', 'old_values', 'new_values'],
                function ($audit) {
                    $dtCreated = "<div style='font-weight: bold; color: #1e293b;'>" . $audit->created_at->format('d/m/Y') . "</div>" .
                                 "<div>" . $audit->created_at->format('H:i:s') . "</div>";

                    $userName = $audit->user->name ?? 'Sistema / Externo';
                    $atorHtml = "<div style='font-weight: bold; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;'>" .
                                "<i class='fa fa-user-circle' style='color: #94a3b8; font-size: 1.25rem;'></i> {$userName}</div>" .
                                "<div style='font-size: 0.7rem; color: #94a3b8; font-family: monospace; margin-top: 0.25rem;'>IP: {$audit->ip_address}</div>";

                    $color = match($audit->event) {
                        'created' => '#10b981', // green
                        'updated' => '#f59e0b', // orange
                        'deleted' => '#ef4444', // red
                        default => '#64748b'
                    };
                    $eventLabel = match($audit->event) {
                        'created' => 'Criação',
                        'updated' => 'Atualização',
                        'deleted' => 'Exclusão',
                        default => ucfirst($audit->event)
                    };

                    $actionHtml = "<span style='background: {$color}15; color: {$color}; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; font-weight: bold; border: 1px solid {$color}40; display: inline-block;'>" .
                                  mb_strtoupper($eventLabel, 'UTF-8') . "</span>";

                    $moduloHtml = "<div style='font-size: 0.85rem; font-weight: bold; color: #334155;'>" . class_basename($audit->auditable_type) . "</div>" .
                                  "<div style='font-size: 0.75rem; color: #94a3b8;'># ID: {$audit->auditable_id}</div>";

                    $diffHtml = "";
                    if(count($audit->old_values ?? []) > 0 || count($audit->new_values ?? []) > 0) {
                        $diffHtml .= "<div style='background: #1e293b; color: #e2e8f0; padding: 0.75rem; border-radius: 0.5rem; font-family: monospace; overflow-y: auto; max-height: 150px; line-height: 1.5; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.2);'>";
                        foreach($audit->new_values ?? [] as $key => $newValue) {
                            $oldValue = $audit->old_values[$key] ?? null;
                            if($oldValue !== $newValue && !in_array($key, ['updated_at', 'created_at'])) {
                                $newValEnc = is_array($newValue) ? json_encode($newValue) : htmlspecialchars((string) $newValue);
                                $oldValEnc = is_array($oldValue) ? json_encode($oldValue) : htmlspecialchars((string) $oldValue);
                                
                                $diffHtml .= "<div style='display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.25rem; word-break: break-all;'>";
                                $diffHtml .= "<span style='color:#94a3b8; font-weight: bold;'>{$key}:</span>";
                                if($audit->event !== 'created') {
                                    $diffHtml .= "<del style='color:#ef4444'>{$oldValEnc}</del> <span style='color:#64748b;'>&#10142;</span>";
                                }
                                $diffHtml .= "<span style='color:#10b981'>{$newValEnc}</span>";
                                $diffHtml .= "</div>";
                            }
                        }
                        $diffHtml .= "</div>";
                    } else {
                        $diffHtml .= "<span style='color:#94a3b8; font-style: italic;'>Nenhum detalhe técnico</span>";
                    }

                    if($audit->url) {
                        $limitUrl = \Illuminate\Support\Str::limit($audit->url, 50);
                        $diffHtml .= "<div style='margin-top: 0.5rem; font-size: 0.7rem; color: #94a3b8;'><i class='fa fa-link'></i> {$limitUrl}</div>";
                    }

                    return [
                        'datahora' => $dtCreated,
                        'ator'     => $atorHtml,
                        'acao'     => $actionHtml,
                        'modulo'   => $moduloHtml,
                        'diff'     => $diffHtml,
                    ];
                }
            )
        );
    }
}
