<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\Backend\User\UserResource;
use App\Http\Resources\Backend\User\UsersCollection;
use App\Http\Resources\Roles\RoleResource;
use App\Http\Resources\Roles\RolesCollection;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    public function getRolesList(Request $request)
    {
        $roles = Role::withCount(['users'])
            ->orderBy('create_at', $request->order)
            ->paginate(20, '*', null, $request->page ?? 1);

        return new RolesCollection($roles);
    }

    public function getRole(Request $request)
    {
        $role = Role::withCount('users')->find($request->id);

        return new RoleResource($role);
    }

    public function saveRole(Request $request)
    {
      if ($required->id) {
        $validator = Validator::make($request->all(), [
          'name' => 'required|unique:roles'
        ]);

        if (!$validator->fails()) {
         return $this->success(false);
        }
      }

      Role::updateOrCreate([
        'id'=> $request->id
      ], [
        'name' => $request->name
      ]);

      return $this->success(true);  
    }

    public function deleteRole(Request $request)
    {
        $success = (boolean)Role::destroy($request->id);

        return $this->success($success);
    }

    public function getUsersList(Request $request)
    {
        $users = User::with('role')
            ->orderBy('created_at', $request->order ?? 'desc')
            ->paginate(20, '*', null, $request->page);

        return new UsersCollection($users);
    }

    public function getUser(Request $request)
    {
        $user = User::with('role')->find($request->id);

        return new UserResource($user);
    }

    public function updateUser(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|exists:users',
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'age' => 'required',
        ]);
        $success = false;

        if (!$validation->fails()) {
            User::updateOne($request, $request->roleId);
            $success = true;
        }


        return $this->success($success);
    }

    public function deleteUser(Request $request)
    {
        $success = (boolean)User::destroy($request->id);

        return $this->success($success);
    }
}
