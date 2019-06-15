<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backend\Room\RoomsCollection;
use App\Http\Resources\Backend\Rooms\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomsController extends Controller
{

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'exists:rooms',
            'number' => 'required|numeric',
            'floor' => 'required|numeric',
            'count_rooms' => 'required|numeric',
            'bed_size' => 'required|numeric',
            'kitchen' => 'required|boolean',
        ]);
        $success = false;

        if (!$validator->fails()) {
            Room::saveOne($request);
            $success = true;
        }

        return $this->success($success);
    }

    public function getBusyRoomNumbers(Request $request)
    {
        $numbers = Room::all()->pluck('number');

        return response()->json($numbers);
    }

    public function getList(Request $request)
    {
        $rooms = Room::getList($request)
            ->with(['images', 'currentUsers'])
            ->paginate(20, '*', '*', $request->page ?? 1);

        return new RoomsCollection($rooms);
    }

    public function getOne(Request $request)
    {
        $room = Room::with(['images', 'currentGuest'])->find($request->id);

        return new RoomResource($room);
    }

    public function remove(Request $request)
    {
        $success = (boolean)Room::destroy($request->id);

        return $this->success($success);
    }
}
