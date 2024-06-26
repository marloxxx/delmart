<?php

namespace App\Http\Resources\Credit;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CreditCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($credit) {
            return CreditResource::make($credit);
        });
    }
}
