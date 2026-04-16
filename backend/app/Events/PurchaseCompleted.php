<?php

namespace App\Events;

use App\Models\Purchase;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The purchase that was just saved.
     */
    public Purchase $purchase;

    /**
     * Create a new event instance.
     *
     * This event is dispatched automatically by the Purchase model via
     * $dispatchesEvents when a new purchase record is created.
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }
}
