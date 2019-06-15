<?php

namespace App\Http\Controllers;

use App\Http\Resources\Roles\RolesCollection;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    public function regist(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'age' => 'required',
            'password' => 'required|min:6',
        ]);
        $success = false;

        if (!$validation->fails()) {
            User::saveOne($request);
            $success = true;
        }

        return $this->success($success);
    }

    public function getRoles(Request $request) {
        $roles = Role::all();

        return new RolesCollection($roles);
    }

    public function acceptRegistration(Request $request)
    {
        $user = User::where('token', $request->token)->first();

        if (!empty($user)) {
            $user->token = null;
            $user->active = 1;
            $user->save();
        }

        return response()->redirectTo('/');
    }

    public function editUser(Request $request)
    {
        $success = User::updateOne($request);

        return $this->success($success);
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validation->fails()) {
            return $this->success(false);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->success(false);
        }

        $user = $request->user();

        if ($user->active) {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;

            if ($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }

            $token->save();

            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        }


        return $this->success(false);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success(true);
    }
}
