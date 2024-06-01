<?php

namespace App\Http\Controllers;

use App\Models\RequestRoom;
use Illuminate\Http\Request;

class RequestRoomController extends Controller
{
    public function index()
    {
        $requests = RequestRoom::all();
        return view('pages.request_rooms.index', compact('requests'));
    }

    public function show(RequestRoom $requestRoom)
    {
        return view('pages.request_rooms.show', compact('requestRoom'));
    }

    public function approve(RequestRoom $requestRoom)
    {
        $requestRoom->update([
            'status' => 'Approved',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Request Room approved successfully',
        ]);
    }

    public function deny(RequestRoom $requestRoom)
    {
        $requestRoom->update([
            'status' => 'Denied',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Request Room denied successfully',
        ]);
    }
}
