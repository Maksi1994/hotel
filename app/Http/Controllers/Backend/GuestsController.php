<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Http\Resources\Backend\Guests\GuestResource;
use App\Http\Resources\Backend\Guests\GuestCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestsController extends Controller
{

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'exists:guests',
            'room_id' => 'required|exists:rooms,id',
            'start' => 'required|numeric',
            'end' => 'required|numeric|different:start',
            'users' => 'required|array|min:1'
        ]);
        $success = false;

        if (!$validation->fails()) {
            Guest::saveOne($request);
            $success = true;
        }

        return $this->success($success);
    }

    public function getOne(Request $request)
    {
      $guest = Guest::where('id', $request->id)->with(['users', 'room'])->get();

      return new GuestResource($guest);
    }

    public function getList(Request $request)
    {
      $guest = Guest::getList($request)->paginate(20, null, null, $request->page);

      return new GuestCollection($guest);
    }

    public function remove(Request $request)
    {
      $success = (boolean)Guest::destroy($request->id);

      return $this->success($success);
    }


}
