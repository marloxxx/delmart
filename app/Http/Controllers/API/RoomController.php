<?php

namespace App\Http\Controllers\API;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Room\RoomCollection;
use App\Http\Resources\Room\RoomResource;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rooms = Room::where('name', 'like', '%' . $request->search . '%')->get();
        return ResponseFormatter::success(
            new RoomCollection($rooms),
            'Data list rooms berhasil diambil'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = Room::find($id);
        if ($room) {
            return ResponseFormatter::success(
                RoomResource::make($room),
                'Data detail room berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data room tidak ada',
                404
            );
        }
    }
}
