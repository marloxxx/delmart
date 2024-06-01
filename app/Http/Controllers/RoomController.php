<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::all();
        return view('pages.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.rooms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ]);
        }

        $image = $request->file('avatar');
        $new_name = rand() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/rooms'), $new_name);

        $request->merge([
            'image' => $new_name,
        ]);

        Room::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Room created successfully',
            'redirect' => route('rooms.index')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        return view('pages.rooms.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ]);
        }

        if ($request->hasFile('avatar')) {
            $image_path = public_path('images/rooms/' . $room->image);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $image = $request->file('avatar');
            $new_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/rooms'), $new_name);

            $request->merge([
                'image' => $new_name,
            ]);
        }

        $room->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Room updated successfully',
            'redirect' => route('rooms.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        $image_path = public_path('images/rooms/' . $room->image);
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $room->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Room deleted successfully',
        ]);
    }
}
