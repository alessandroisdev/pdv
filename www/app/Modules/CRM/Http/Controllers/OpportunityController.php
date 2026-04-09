<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CRM\Models\Opportunity;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function board()
    {
        // Pipeline states
        $stages = [
            'PROSPECT' => 'Prospecção',
            'NEGOTIATION' => 'Negociação',
            'CONTRACT' => 'Contrato/Aprovação',
            'WON' => 'Vendido (Ganho)',
            'LOST' => 'Perdido (Lost)'
        ];

        // Retrieve ordered opportunities and group by stage
        $allOps = Opportunity::with('customer')->orderBy('order')->orderBy('updated_at', 'desc')->get();
        
        $lanes = [];
        foreach ($stages as $key => $label) {
            $lanes[$key] = [
                'label' => $label,
                'items' => $allOps->where('stage', $key)->values()
            ];
        }

        return view('crm::opportunities.board', compact('lanes'));
    }

    public function updateStage(Request $request, Opportunity $opportunity)
    {
        $payload = $request->validate([
            'stage' => 'required|string',
            'order' => 'integer'
        ]);

        $opportunity->update([
            'stage' => $payload['stage'],
            'order' => $payload['order'] ?? $opportunity->order
        ]);

        return response()->json(['success' => true]);
    }
}
