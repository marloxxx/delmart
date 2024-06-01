<?php

namespace App\Http\Resources\RequestRoom;

use App\Http\Resources\Room\RoomResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestRoomResource extends JsonResource
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
            'room' => RoomResource::make($this->room),
            'description' => $this->description,
            'status' => $this->status,
            // format date example: Senin, 1 Januari 2021 00:00
            'startDate' => Carbon::parse($this->start_date)->format('l, d F Y H:i'),
            'endDate' => Carbon::parse($this->end_date)->format('l, d F Y H:i'),
            'createdAt' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updatedAt' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
