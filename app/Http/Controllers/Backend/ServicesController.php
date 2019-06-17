<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Resources\Backend\Services\ServiceResource;
use App\Http\Resources\Backend\Services\ServiceCollection;

class ServicesController extends Controller
{
    public function save(Request $request) {
      $validation = null;

      if ($request->id) {
        $validation = Validator::make($request->all(), [
          'id' => 'required|exists:service',
          'name' => 'required',
          'options.*.name' => 'required'
        ]);
      } else {
        $validation = Validator::make($request->all(), [
          'name' => 'required',
          'options.*.name' => 'required'
        ]);
      }

      if ($validation->fails()) {
        return $this->success(false);
      }

      Service::saveOne($request);

      return $this->success(true);
    }

    public function getOne() {
      $service = Service::with('options')->withCount('guests')->find($request->id);

    
    }

    public function getList(Request $request) {
      $services = Service::with('options')
      ->withCount('guests')
      ->getList($request)
      ->paginate(20, null, null, $request->page ?? 1);

      return new ServiceCollection($services);
    }

    public function delete(Request $request) {
        Service::destroy($request->id);

        return $this->success(true);
    }
}
