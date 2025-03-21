<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampingSeason extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'camping_seasons';

    public function getAllPaginate()
    {
        return self::orderBy('id', 'DESC')->paginate(10);
    }

    public function getAllActive()
    {
        return self::all();
    }

    public function createCampingSeason($data = [])
    {
        return self::create($data);
    }

    public function findCampingSeason($id)
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
}
