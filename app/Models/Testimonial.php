<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'customer_photo', 'customer_title',
        'content', 'rating', 'status', 'order',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
