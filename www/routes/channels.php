<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Regra do Reverb: Bloqueia quem não possui credencial Auth válida tentando escutar a Fila
Broadcast::channel('kds.branch.{branchId}', function ($user, $branchId) {
    // Retorna true somente se o usuário logado no portal interno pertencer ao mesmo Branch
    // Por ser MVP vamos abrir para todos os admin authenticados
    return (int) $user->branch_id === (int) $branchId;
});
