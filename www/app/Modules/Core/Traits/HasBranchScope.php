<?php

namespace App\Modules\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasBranchScope
{
    /**
     * Boot the trait and apply the global scope.
     */
    protected static function bootHasBranchScope()
    {
        // Se a entidade não for "isolada" (como Products), nós a deixamos global,
        // mas as Vendas e Estoques ganham este Trait e não vazam de jeito nenhum.
        static::addGlobalScope('branch', function (Builder $builder) {
            // Em ambiente console ou sem requisição (Workers), não aplicamos caso não tenha sessao.
            // Para Jobs de Sefaz, eles já carregam o Model pronto, não afeta.
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }

            // Idealmente, a Filial estaria na Sessão ou no Token JWT do Request.
            // Vamos adotar "1" (Matriz) como Standard em nosso MVP se não setado.
            $currentBranchId = session('current_branch_id', 1);

            $builder->where($builder->getModel()->getTable() . '.branch_id', $currentBranchId);
        });
    }
}
