<?php

namespace App\Models;

use Database\Factories\RestaurantTableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    /** @use HasFactory<RestaurantTableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'table_number',
        'capacity',
        'location',
        'status',
        'qr_code',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($table) {
            if (empty($table->qr_code)) {
                $table->qr_code = Str::uuid();
            }
        });
    }

    /**
     * Get the orders for this table.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    /**
     * Get the current active order for this table.
     */
    public function currentOrder(): HasOne
    {
        return $this->hasOne(Order::class, 'table_id')
            ->whereIn('status', ['pending', 'preparing', 'ready', 'served'])
            ->latest();
    }

    /**
     * Get the reservations for this table.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'table_id');
    }

    /**
     * Scope a query to only include available tables.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include occupied tables.
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    /**
     * Mark table as occupied.
     */
    public function markOccupied(): void
    {
        $this->update(['status' => 'occupied']);
    }

    /**
     * Mark table as available.
     */
    public function markAvailable(): void
    {
        $this->update(['status' => 'available']);
    }
}
