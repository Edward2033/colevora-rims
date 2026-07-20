<?php

namespace App\Models;

use Database\Factories\InventoryItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    /** @use HasFactory<InventoryItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'unit',
        'quantity',
        'minimum_quantity',
        'cost_price',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'minimum_quantity' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    /**
     * Get the supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the stock transactions.
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Get the food ingredients using this item.
     */
    public function foodIngredients(): HasMany
    {
        return $this->hasMany(FoodIngredient::class);
    }

    /**
     * Get the alerts for this item.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(InventoryAlert::class);
    }

    /**
     * Scope a query to only include active items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to low stock items.
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_quantity');
    }

    /**
     * Check if item is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_quantity;
    }

    /**
     * Add stock quantity.
     */
    public function addStock(float $quantity, User $user, string $type = 'purchase', ?string $notes = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $this->increment('quantity', $quantity);

        StockTransaction::create([
            'inventory_item_id' => $this->id,
            'type' => $type,
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => $user->id,
            'notes' => $notes,
        ]);

        // Resolve low stock alert if quantity is now above minimum
        if (! $this->isLowStock()) {
            $this->alerts()->where('status', 'active')->update(['status' => 'resolved']);
        }
    }

    /**
     * Reduce stock quantity.
     */
    public function reduceStock(float $quantity, User $user, string $type = 'usage', ?string $notes = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $this->decrement('quantity', $quantity);

        StockTransaction::create([
            'inventory_item_id' => $this->id,
            'type' => $type,
            'quantity' => -$quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => $user->id,
            'notes' => $notes,
        ]);

        // Generate low stock alert if needed
        if ($this->isLowStock()) {
            $this->generateLowStockAlert();
        }
    }

    /**
     * Generate low stock alert.
     */
    public function generateLowStockAlert(): void
    {
        // Check if there's already an active alert
        $existingAlert = $this->alerts()->where('status', 'active')->first();

        if (! $existingAlert) {
            InventoryAlert::create([
                'inventory_item_id' => $this->id,
                'message' => "Low stock alert: {$this->name} is below minimum quantity ({$this->quantity} {$this->unit} remaining, minimum: {$this->minimum_quantity} {$this->unit})",
                'status' => 'active',
            ]);
        }
    }
}
