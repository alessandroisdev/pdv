<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class DataTableService
{
    /**
     * Processa uma query Builder do Eloquent e formata para o DataTables (Server-Side).
     *
     * @param Builder $query Query base
     * @param Request $request POST request contendo padronizações length, start, search, columns, order
     * @param array $searchableColumns Colunas autorizadas p/ LIKE %% (eg: ['name', 'description'])
     * @param callable|null $formatter Função anonima opcional para adicionar colunas de Action/Botões
     * @return array Array estruturado p/ json (DataTables spec)
     */
    public static function process(Builder $query, Request $request, array $searchableColumns = [], callable $formatter = null): array
    {
        try {
            $draw = intval($request->input('draw', 1));
            $start = intval($request->input('start', 0));
            $length = intval($request->input('length', 10));
            $search = $request->input('search.value', '');
            
            // Colunas mapeadas pela requisição DataTables 
            // O request['columns'] aponta orderavelmente quais DB fields são.
            $columns = $request->input('columns', []);
            $order = $request->input('order', []);

            // Total de Registros SEM filtro
            $recordsTotal = (clone $query)->count();

            // 1. Aplicação de Buscas Múltiplas (Search)
            if (!empty($search) && !empty($searchableColumns)) {
                $query->where(function ($q) use ($searchableColumns, $search) {
                    foreach ($searchableColumns as $index => $col) {
                        if ($index === 0) {
                            $q->where($col, 'LIKE', "%{$search}%");
                        } else {
                            $q->orWhere($col, 'LIKE', "%{$search}%");
                        }
                    }
                });
            }

            // Total de Registros COM filtro
            $recordsFiltered = (clone $query)->count();

            // 2. Ordenação (Order)
            if (!empty($order)) {
                foreach ($order as $ord) {
                    $columnIndex = intval($ord['column']);
                    if (isset($columns[$columnIndex]) && $columns[$columnIndex]['orderable'] !== 'false') {
                        // DataTables aceita 'name' para mapear um campo DB real quando 'data' for virtual
                        $columnName = !empty($columns[$columnIndex]['name']) ? $columns[$columnIndex]['name'] : $columns[$columnIndex]['data'];
                        $dir = strtolower($ord['dir']) === 'asc' ? 'asc' : 'desc';
                        // Evita ordernar por colunas puramente visuais como 'acoes' ou as que contem HTML interno se nao mapeadas no 'name'
                        if ($columnName && $columnName !== 'acoes' && $columnName !== 'actions' && !str_contains($columnName, 'html')) {
                            // Ignora order by virtual data se contem underscore mas nao ta mapeado (heuristic) 
                            // a menos que o dev indique o name correto.
                            $query->orderBy($columnName, $dir);
                        }
                    }
                }
            } else {
                // Default fallback
                $query->latest('id');
            }

            // 3. Paginação (Limit Offset)
            // Se length for -1, mostra todos.
            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            // 4. Execução e Mapeamento
            $records = $query->get();
            $data = [];

            foreach ($records as $item) {
                $row = $item->toArray();
                
                if ($formatter) {
                    // Injecta ou sobresscreve atributos (ex: actions HTML)
                    $row = array_merge($row, $formatter($item));
                }
                
                $data[] = $row;
            }

            return [
                "draw"            => $draw,
                "recordsTotal"    => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data"            => $data
            ];
            
        } catch (\Exception $e) {
            Log::error('DataTableService Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                "draw"            => intval($request->input('draw', 1)),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => [],
                "error"           => "Erro ao processar listagem de dados. Detalhes: " . $e->getMessage()
            ];
        }
    }
}
