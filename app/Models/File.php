<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory;
    use SoftDeletes;

    public static function existsByPath($path): bool
    {
        $count = self::where('path', '=', $path)->count();
        return $count >= 1;
    }

    public static function insert($data)
    {
        $data['created_at'] = new \DateTime();
        return self::insertGetId($data);
    }

}
