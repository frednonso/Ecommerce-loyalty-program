<?php

namespace App\Models;

use App\Events\PurchaseCompleted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'amount',
    ];


    // fires this event automatically when new record is saved
    
    protected $dispatchesEvents = [
        'created' => PurchaseCompleted::class,
    ];

    /**
     * Get the user that made this purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
