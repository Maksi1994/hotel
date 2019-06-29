<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resources\Backend\WorkersCollection;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkersController extends Controller
{

    public function getWorkingsHistory(Request $request)
    {
        $guests = User::withCount('workings')
            ->getWorkersList($request)
            ->paginate(20, null, null, $request->page ?? 1);

        return new WorkersCollection($guests);
    }

    public function assignWorkerOnGuest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|exists:guests',
            'workers.*' => 'required|min:1|exists:users,id'
        ]);
        $success = false;

        if (!$validation->fails()) {
            Guest::find($request->id)->assignWorkers($request);
        }

        return $this->success($success);
    }


}
