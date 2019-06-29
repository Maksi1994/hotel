<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Guest extends Model
{

    public $timestamps = true;
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'guest_user', 'visit_id', 'user_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'guest_service', 'guest_id', 'service_id');
    }

    public function workers() {
        return $this->belongsToMany(User::class , 'guest_worker', 'guest_id', 'worker_id');
    }

    public function scopeGetList($query, Request $request)
    {
        if ($request->view === 'regular') {
            $query = $query->selectRaw('
          guests.id,
          guests.start,
          guests.end,
          rooms.floor,
          rooms.number as room_number,
          COUNT(guest_user.user_id) as users_count
        ')->join('rooms', 'room_id', '=', 'rooms.id')
                ->join('guest_user', 'guests.id', '=', 'guest_user.visit_id')
                ->groupBy('guests.id');

            $query->when($request->order_by == 'date' && $request->order, function ($q) use ($request) {
                return $q->orderBy('guests.created_at', $request->order);
            });
        } else if ($request->view === 'users') {
            $query = $query->selectRaw('
          users.name,
          COUNT(guests.id) as count_visits
        ')->join('guest_user', 'guests.id', '=', 'guest_user.visit_id')
                ->join('users', 'guest_user.user_id', '=', 'users.id')
                ->groupBy('guest_user.user_id');

            $query->when($request->order_by == 'often_user' && $request->order, function ($q) use ($request) {
                return $q->orderBy('count_visits', $request->order);
            });
        }

        return $query;
    }

    public static function saveOne(Request $request)
    {
        $request->merge([
            'start' => Carbon::createFromTimestamp($request->start / 1000)->toDateTimeString(),
            'end' => Carbon::createFromTimestamp($request->end / 1000)->toDateTimeString(),
        ]);

        $guest = self::updateOrCreate(
            ['id' => $request->id],
            $request->only([
                'room_id',
                'start',
                'end'
            ])
        );

        $guest->users()->sync($request->users);
    }

    public function assignWorkers($request) {
        $this->workers()->async($request->workers);
    }

}
