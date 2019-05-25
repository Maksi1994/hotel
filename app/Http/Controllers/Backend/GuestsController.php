<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestsController extends Controller
{

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'room_id' => 'required',
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

    }

    public function getList(Request $request)
    {

    }

    public function remove(Request $request)
    {

    }


}
