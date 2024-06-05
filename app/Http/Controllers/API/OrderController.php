<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Order\OrderResource;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', Auth::user()->id)
            ->where('status', 'like', '%' . $request->status . '%')
            ->orderBy('created_at', 'DESC')->get();
        return ResponseFormatter::success(
            new OrderCollection($orders),
            'Data list order berhasil diambil'
        );
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     */
    public function show($id)
    {
        $order = Order::find($id);
        if ($order) {
            return ResponseFormatter::success(
                OrderResource::make($order),
                'Data detail order berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data order tidak ada',
                404
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        $data = [];
        $data['user_id'] = Auth::user()->id;
        $data['code'] = 'ORD-' . mt_rand(10000, 99999);
        $data['total'] = 0;
        $carts = Cart::where('user_id', Auth::user()->id)->get();
        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);
            $data['total'] += $product->price * $cart->quantity;
            $product->quantity -= $cart->quantity;
            $product->save();
        }
        $data['description'] = $request->description;
        $data['payment_method'] = $request->payment_method;
        $data['status'] = 'Pending';
        $order = Order::create($data);

        foreach ($carts as $cart) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
            ]);
        }

        // Delete cart
        $carts->each->delete();

        return ResponseFormatter::success(
            OrderResource::make($order),
            'Data order berhasil ditambahkan'
        );
    }

    /**
     * Cancel order by user
     *
     */
    public function cancel($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->status = 'Cancel';
            $order->save();
            return ResponseFormatter::success(
                OrderResource::make($order),
                'Data order berhasil diupdate'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data order tidak ada',
                404
            );
        }
    }
}
