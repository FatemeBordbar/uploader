<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users_roles';
    protected $fillable = [
        'user_id', 'role_id'
    ];

    public static function insert($user_id, $role_id)
    {
        $d = self::create(['user_id' => $user_id, 'role_id' => $role_id , 'created_at' => new \DateTime()]);
        return $d->id;
    }

    public static function removeRolesForUser($user_id)
    {
        $o = self::where('user_id', '=', $user_id)->forcedelete();
        return $o;
    }

    public static function getRolesForUser($user_id)
    {
        $o = self::where('user_id', '=', $user_id)->get();
        return $o;
    }
}
