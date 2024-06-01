<?php

namespace App\Http\Controllers\API;

use App\Models\RequestRoom;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\RequestRoom\RequestRoomCollection;
use App\Http\Resources\RequestRoom\RequestRoomResource;

class RequestRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requestRooms = RequestRoom::where('user_id', Auth::user()->id)->where('status', 'like', '%' . $request->status . '%')
            ->orderBy('created_at', 'DESC')->get();
        return ResponseFormatter::success(
            new RequestRoomCollection($requestRooms),
            'Data list request rooms berhasil diambil'
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
        $requestRoom = RequestRoom::find($id);
        if ($requestRoom) {
            return ResponseFormatter::success(
                $requestRoom,
                'Data detail request room berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data request room tidak ada',
                404
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestRoom = RequestRoom::create([
            'user_id' => Auth::user()->id, // auth('api')->user() = user yang sedang login (user yang sedang login adalah user yang membuat request room
            'room_id' => $request->room_id,
            'description' => $request->description,
            'status' => 'Pending',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return ResponseFormatter::success(
            RequestRoomResource::make($requestRoom),
            'Data request room berhasil ditambahkan'
        );
    }

    /**
     * Update status request room to canceled.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        $requestRoom = RequestRoom::find($id);
        if ($requestRoom) {
            $requestRoom->update([
                'status' => 'Cancelled',
            ]);

            return ResponseFormatter::success(
                RequestRoomResource::make($requestRoom),
                'Data request room berhasil dibatalkan'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data request room tidak ada',
                404
            );
        }
    }
}
