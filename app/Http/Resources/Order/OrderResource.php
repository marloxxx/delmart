<?php

namespace App\Http\Resources\Order;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'total' => doubleval($this->total),
            'paymentMethod' => $this->payment_method,
            'description' => $this->description,
            'orderDetails' => OrderDetailResource::collection($this->orderDetails),
            'status' => $this->status,
            'createdAt' => Carbon::parse($this->created_at)->format('d F Y'),
        ];
    }
}
