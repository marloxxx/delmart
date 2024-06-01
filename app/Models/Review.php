<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_detail_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the review's rating.
     * return is performed by the Review model
     */
    public function getRatingAttribute()
    {
        // get average rating
        $average = floor($this->avg('rating'));
        // return the average rating
        return $average;
    }
}
