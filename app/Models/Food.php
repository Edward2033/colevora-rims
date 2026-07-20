<?php

namespace App\Models;

use Database\Factories\FoodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Food extends Model
{
    /** @use HasFactory<FoodFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'food';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'image',
        'price',
        'discount_price',
        'availability',
        'status',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'availability' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the food.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who created this food item.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the price changes for this food item.
     */
    public function priceChanges(): HasMany
    {
        return $this->hasMany(FoodPriceChange::class);
    }

    /**
     * Get the assignments for this food item.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(FoodAssignment::class);
    }

    /**
     * Get the employees assigned to this food item.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'food_assignments')
            ->withPivot('assigned_by', 'status')
            ->withTimestamps();
    }

    /**
     * Get the ingredients for this food item.
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(FoodIngredient::class);
    }

    /**
     * Scope a query to only include active food items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include available food items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability', true);
    }

    /**
     * Get the effective price (discount price if available, otherwise regular price).
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Check if food item has a discount.
     */
    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }
}
