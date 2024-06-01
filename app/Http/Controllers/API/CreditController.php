<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\Credit;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Credit\CreditCollection;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $credits = Credit::where('provider', 'like', '%' . $request->provider . '%')
            ->orderBy('nominal', 'ASC')->get();
        return ResponseFormatter::success(
            new CreditCollection($credits),
            'Data list products berhasil diambil'
        );
    }

    public function show($id)
    {
        $credit = Credit::findOrFail($id);
        return ResponseFormatter::success(
            new CreditCollection($credit),
            'Data detail product berhasil diambil'
        );
    }

    public function checkout(Request $request)
    {
        $credit = Credit::findOrFail($request->credit_id);
        $data['user_id'] = Auth::user()->id;
        $data['code'] = 'ORD-' . mt_rand(10000, 99999);
        $data['total'] = $credit->price;
        $data['description'] = $request->description;
        $data['payment_method'] = $request->payment_method;
        $data['status'] = 'Completed';
        $order = Order::create($data);

        return ResponseFormatter::success(
            OrderResource::make($order),
            'Data order berhasil ditambahkan'
        );
    }
}
