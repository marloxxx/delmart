<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return view('pages.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('pages.orders.show', compact('order'));
    }

    public function process(Order $order)
    {
        $order->update([
            'status' => 'processed',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order processed successfully',
        ]);
    }

    public function deny(Order $order)
    {
        $order->update([
            'status' => 'denied',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order denied successfully',
        ]);
    }
}
