<?php

namespace App\Events;

use App\Modules\Sales\Models\Sale;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOmnichannelOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $salePayload;
    public $branchId;

    /**
     * Create a new event instance.
     */
    public function __construct(Sale $sale)
    {
        // Precisamos expor no Payload as propriedades amigáveis 
        // para que o Objeto JS no Frontend recupere sem precisar dar F5 no Laravel.
        $this->branchId = $sale->branch_id;
        $this->salePayload = [
            'id' => $sale->id,
            'amount' => number_format($sale->total_cents / 100, 2, ',', '.'),
            'timestamp' => $sale->created_at->format('H:i'),
            'customer' => $sale->customer_name ?? 'Autoatendimento',
            'status' => $sale->status,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Envia apenas pro canal seguro da Loja especifica
        return [
            new PrivateChannel('kds.branch.' . $this->branchId),
        ];
    }

    public function broadcastAs(): string
    {
        // Como o Vue/JS Echo vai chamar a função de receptor
        return 'new.order';
    }
}
