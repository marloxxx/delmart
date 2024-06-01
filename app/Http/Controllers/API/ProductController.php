<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->search . '%')->whereNotIn('category', ['pulsa'])->get();
        return ResponseFormatter::success(
            new ProductCollection($products),
            'Data list products berhasil diambil'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if ($product) {
            return ResponseFormatter::success(
                ProductResource::make($product),
                'Data detail product berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data product tidak ada',
                404
            );
        }
    }
}
