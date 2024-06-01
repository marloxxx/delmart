<?php

namespace App\Http\Resources\RequestRoom;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RequestRoomCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($item) {
            return new RequestRoomResource($item);
        });
    }
}
