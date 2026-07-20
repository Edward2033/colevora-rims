<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'transaction_reference',
        'status',
        'paid_by',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Virtual tax_amount attribute (estimated at 10% of amount).
     */
    public function getTaxAmountAttribute(): float
    {
        return round((float) $this->amount * 0.10, 2);
    }

    /**
     * Alias payment_status -> status for legacy dashboard code.
     */
    public function getPaymentStatusAttribute(): string
    {
        return $this->status ?? 'pending';
    }

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who processed the payment.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Scope a query to completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark payment as completed.
     */
    public function markCompleted(User $processor): void
    {
        $this->update([
            'status' => 'completed',
            'paid_by' => $processor->id,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
