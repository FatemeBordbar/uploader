<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
//use App\Http\Controllers\Api\V1\CommonController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Patterns\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected  $common ,$model, $repository;

    function __construct( User $model)
    {
        $this->common = new CommonController();
        $this->repository = new Repository($model);
        $this->model = $model;
    }

    function register()
    {
        $parameters = ['username', 'password','role'];
        $a = Common::shaveInputAPI($parameters);

        $validator = Validator::make($a, RULES_REGISTER);
        if ($validator->fails()) {
            $this->common->response(false, $validator->errors(), NULL, HTTP_BAD_REQUEST);
        }

        $user = User::exists($a['username']);
        if ($user != null) {
            $this->common->response(false, USERNAME_ALREADY_EXISTS, NULL, HTTP_BAD_REQUEST);
        }

        {
            DB::beginTransaction();
            $inserted = $this->repository->create([
                'username' => $a['username'],
                'password' => Hash::make($a['password']),
                'has_access' => 1
            ]);
            $roles = UserRole::insert($inserted->id, $a['role']);

            if( !$inserted || !$roles )
            {
                DB::rollBack();
            } else {
                DB::commit();
            }
        }

        $access_token = $this->common->getAccessToken($a['username'], $a['password']);
        $this->common->response(true, null, $access_token, HTTP_OK);

    }
}
