<?php

use App\Models\Review;

function getRatingCount($id)
{
    // get average rating
    $rating = Review::join('order_details', 'reviews.order_detail_id', '=', 'order_details.id')
        ->join('products', 'order_details.product_id', '=', 'products.id')
        ->where('products.id', $id)
        ->get();
    $rating = $rating->pluck('rating');
    $average = floor($rating->avg());
    // return the average rating
    return $average;
}
