<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\Backend\Services\ServicesCollection;
use App\Http\Resources\Backend\Services\ServicesResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;

class ServicesController extends Controller
{
    public function save(Request $request)
    {
        $validation = null;

        if ($request->id) {
            $validation = Validator::make($request->all(), [
                'id' => 'required|exists:services',
                'name' => 'required',
                'options.*.name' => 'required'
            ]);
        } else {
            $validation = Validator::make($request->all(), [
                'name' => 'required|unique:services,name',
                'options.*.name' => 'required'
            ]);
        }

        if ($validation->fails()) {
            return $this->success(false);
        }

        Service::saveOne($request);

        return $this->success(true);
    }

    public function assingService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:services,id',
            'roles.*' => 'required|min:1|exists:roles,id'
        ]);
        $success = false;

        if (!$validator->fails()) {
            Service::find($request->id)->roles()->async($request->roles);
            $success = true;
        }

        return $this->success($success);
    }

    public function getOne(Request $request)
    {
        $service = Service::with('options')
            ->withCount('guests')
            ->find($request->id);

        return new ServicesResource($service);
    }

    public function getList(Request $request)
    {
        $services = Service::with('options')
            ->withCount('guests')
            ->getList($request)
            ->paginate(20, null, null, $request->page ?? 1);

        return new ServicesCollection($services);
    }

    public function delete(Request $request)
    {
        Service::destroy($request->id);

        return $this->success(true);
    }
}
