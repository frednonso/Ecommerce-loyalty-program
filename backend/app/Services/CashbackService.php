<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CashbackService
{
    /**
     * Fixed cashback reward amount in Naira.
     */
    public const AMOUNT = 300;

    /**
     * Disburse cashback to a user for unlocking a badge.
     *
     * Currently this is a mock implementation that writes a structured entry
     * to storage/logs/cashback.log.  To swap in a real payment provider,
     * replace the body of this method (or inject a PaymentGatewayInterface)
     * without touching the listener.
     *
     * @param  User  $user  The recipient of the cashback.
     * @param  Badge $badge The badge that triggered the reward.
     * @return void
     */
    public function disburse(User $user, Badge $badge): void
    {
        $timestamp = now()->toDateTimeString();
        $amount    = self::AMOUNT;
        $currency  = '₦';

        // Write to a dedicated cashback log channel so these records stay
        // separated from the generic application log.
        Log::channel('cashback')->info('Cashback disbursed', [
            'timestamp' => $timestamp,
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'badge'     => $badge->name,
            'amount'    => "{$currency}{$amount}",
        ]);
    }
}
