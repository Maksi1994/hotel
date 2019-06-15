<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Role;
use App\Notifications\RegistUser;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function avatars()
    {
        return $this->morphMany(Image::class, 'attachable');
    }

    public function visiting()
    {
        return $this->belongsToMany(Guest::class, 'user_guest', 'guest_id', 'user_id');
    }

    public function hasAccess($type)
    {
        return $this->role()->where('name', $type)->exists();
    }

    public static function saveOne(Request $request)
    {
        $avatar = '';

        if ($request->hasFile('img')) {
            $request->merge([
                'avatar' => $avatar
            ]);
        }

        $user = self::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role_id' => Role::getRegularUserId(),
            'age' => $request->age,
            'password' => Hash::make($request->password),
            'active' => 0,
            'token' => Str::random(32),
            'avatar' => $avatar
        ]);

        $user->notify(new RegistUser($user));
    }

    public function updateOne(Request $request, $roleId = null)
    {
        $avatar = '';
        $userModel = self::where([
            'id' => $request->id,
            'active' => 1
        ])->first();
        $updatedData = null;

        if ($userModel) {
            if ($request->hasFile('img')) {
                if ($userModel->avatar) {

                }
                $request->merge([
                    'avatar' => $avatar
                ]);
            }

            $updatedData = $request->only([
                'first_name',
                'last_name',
                'age',
                'avatar'
            ]);

            if ($roleId) {
                $updatedData['role_id'] = $roleId;
            }

            $userModel->update($updatedData);
        }
    }

}
