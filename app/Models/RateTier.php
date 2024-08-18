<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateTier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getAll()
    {
        return self::all();
    }

    public function whereFirst($where = [])
    {
        return self::where($where)->first();
    }

    public function getAllPaginate()
    {
        return self::paginate(10);
    }

    public function getAllActive()
    {
        return self::where('status' ,1)->get();
    }

    public function createRateTier($data = [])
    {
        return self::create($data);
    }

    public function findRateTier($id)
    {
        return self::find($id);
    }

    public function whereGet($where = [])
    {
        return self::where($where)->get();
    }

    public function whereUpdate($where = [], $data = [])
    {
        return self::where($where)->update($data);
    }
}
