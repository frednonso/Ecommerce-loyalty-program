<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Services\CashbackService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCashbackOnBadgeUnlock implements ShouldQueue
{
    /**
     * Create the listener.
     *
     * Laravel's service container automatically injects CashbackService here,
     * making it trivial to swap the underlying payment provider by simply
     * rebinding the class in AppServiceProvider.
     */
    public function __construct(protected CashbackService $cashback) {}

    /**
     * Handle the BadgeUnlocked event.
     *
     * Delegates the actual disbursement to CashbackService so this listener
     * stays thin and the payment logic is swappable without touching the
     * event/listener wiring.
     */
    public function handle(BadgeUnlocked $event): void
    {
        $this->cashback->disburse($event->user, $event->badge);
    }
}
