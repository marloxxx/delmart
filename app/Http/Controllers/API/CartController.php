<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartCollection;
use App\Http\Resources\Cart\CartResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->get();
        return ResponseFormatter::success(
            new CartCollection($carts),
            'Data list cart berhasil diambil'
        );
    }

    /**
     * store cart to storage.
     *
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $cart = Cart::where('user_id', Auth::user()->id)->where('product_id', $data['product_id'])->first();
        if ($cart) {
            $cart->quantity = $cart->quantity + $data['quantity'];
            $cart->save();
            return ResponseFormatter::success(
                $cart,
                'Data cart berhasil ditambahkan'
            );
        } else {
            $cart = Cart::create($data);
            return ResponseFormatter::success(
                $cart,
                'Data cart berhasil ditambahkan'
            );
        }
    }

    /**
     * increase the specified resource from storage.
     */
    public function increase($id)
    {
        $item = Cart::findOrFail($id);
        $item->quantity = $item->quantity + 1;
        $item->save();
        return ResponseFormatter::success(
            CartResource::make($item),
            'Data cart berhasil ditambahkan'
        );
    }
    /**
     * update the specified resource from storage.
     *
     */
    public function update($id, Request $request)
    {
        $item = Cart::findOrFail($id);
        $item->quantity = $request->quantity;
        $item->save();
        return ResponseFormatter::success(
            CartResource::make($item),
            'Data cart berhasil diupdate'
        );
    }

    /**
     * decrease the specified resource from storage.
     *
     */
    public function decrease($id)
    {
        $item = Cart::findOrFail($id);
        if ($item->quantity > 1) {
            $item->quantity = $item->quantity - 1;
            $item->save();
            return ResponseFormatter::success(
                CartResource::make($item),
                'Data cart berhasil dikurangi'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data cart tidak bisa dikurangi',
                500
            );
        }
    }

    /**
     * remove the specified resource from storage.
     *
     */
    public function destroy($id)
    {
        $item = Cart::findOrFail($id);
        $item->delete();
        return ResponseFormatter::success(
            $item,
            'Data cart berhasil dihapus'
        );
    }

    /**
     * clear the specified resource from storage.
     *
     */
    public function clear()
    {
        $items = Cart::where('user_id', Auth::user()->id)->get();
        foreach ($items as $item) {
            $item->delete();
        }
        return ResponseFormatter::success(
            $items,
            'Data cart berhasil dikosongkan'
        );
    }
}
