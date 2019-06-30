<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;

class EstimatesController extends Controller
{
    public function toggleEstimate(Request $request) {
      $model = null;
      $validation = Validator::make($request->all(), [
        'guest_id'=> 'required|exists:guests,id',
        'value'=> 'required|numeric|min:1|max:5',
        'model_type' => 'required|in:room,worker'
      ]);
      $success = false;

      if (!$validation->fails()) {
        switch ($request->model_type) {
          case 'room':
            $model = Room::find($request->model_id);
            break;
          case 'worker':
            $model = User::find($request->model_id);
        }

        if (!empty($model->estimate)) {
            $model->estimate()->delete();
        } else {
            $model->estimate()->create([
              'guest_id' => $request->guest_id,
              'value' => $request->value
            ]);
        }

        $success = true;
      }

      return $this->success($success);
    }

    public function getEstimates(Request $request) {
      $estimates = Estimate::getList($request)->paginate(20, null, null, $request->page ?? 1);

    //  return new 
    }
}
