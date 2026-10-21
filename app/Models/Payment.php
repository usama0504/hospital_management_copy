<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const FAILED = 'failed';

    public const GATEWAY_JAZZCASH_MANUAL = 'jazzcash_manual';

    protected $fillable = [
        'bill_id',
        'patient_id',
        'gateway',
        'reference',
        'transaction_id',
        'sender_number',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'paid_at',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class)->withTrashed();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
