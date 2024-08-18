<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getAllPaginate()
    {
        return self::orderBy('id', 'DESC')->paginate(5);
    }

    public function getAllActive()
    {
        return self::where('status', 1)->get();
    }

    public function createEvent($data = [])
    {
        return self::create($data);
    }

    public function findEvent($id)
    {
        return self::find($id);
    }

    public function whereGet($where = [])
    {
        return self::where($where)->get();
    }

    public function whereFirst($where = [])
    {
        return self::where($where)->first();
    }

    public function whereUpdate($where = [], $data = [])
    {
        return self::where($where)->update($data);
    }

    public function getEventsByCidCod($cid, $cod)
    {

        return self::where(function ($query) use ($cid, $cod) {
            $query->where(function ($query) use ($cid, $cod) {
                $query->where('eventstart', '<=', $cid)
                    ->where('eventend', '>=', $cid);
            })->orWhere(function ($query) use ($cid, $cod) {
                $query->where('eventstart', '<=', $cod)
                    ->where('eventend', '>=', $cod);
            })->orWhere(function ($query) use ($cid, $cod) {
                $query->where('eventstart', '>=', $cid)
                    ->where('eventend', '<=', $cod);
            });
        })->get();
    }

    public function getUpcomingEvents()
    {
        return self::where('eventstart', '>', Carbon::now())
            ->orderBy('eventstart', 'asc')
            ->get();
    }
}
