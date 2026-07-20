<?php

namespace App\Models;

use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /** @use HasFactory<CartFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'status',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope a query to only include active carts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Add an item to the cart.
     */
    public function addItem(Food $food, int $quantity = 1): CartItem
    {
        $item = $this->items()->where('food_id', $food->id)->first();

        if ($item) {
            $item->increment('quantity', $quantity);

            return $item->fresh();
        }

        return $this->items()->create([
            'food_id' => $food->id,
            'quantity' => $quantity,
            'price' => $food->effective_price,
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $foodId): void
    {
        $this->items()->where('food_id', $foodId)->delete();
    }

    /**
     * Update item quantity.
     */
    public function updateItemQuantity(int $foodId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($foodId);

            return;
        }

        $this->items()->where('food_id', $foodId)->update(['quantity' => $quantity]);
    }

    /**
     * Calculate cart subtotal.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): void
    {
        $this->items()->delete();
    }

    /**
     * Calculate cart subtotal.
     */
    public function calculateSubtotal(): float
    {
        return $this->items->sum(fn ($item) => $item->price * $item->quantity);
    }

    /**
     * Calculate cart total (with tax).
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        $tax = $subtotal * ((float) SiteSetting::get('tax_rate', 10) / 100);

        return $subtotal + $tax;
    }

    /**
     * Calculate cart tax amount.
     */
    public function calculateTax(): float
    {
        $subtotal = $this->calculateSubtotal();

        return $subtotal * ((float) SiteSetting::get('tax_rate', 10) / 100);
    }

    /**
     * Get total item count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
