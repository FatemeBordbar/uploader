<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    protected $common;

    function __construct()
    {
        $this->common = new CommonController();
    }

    public function login(){
        $parameters = ['username', 'password'];
        $a = Common::shaveInputAPI($parameters);

        $validator = Validator::make($a, RULES_LOGIN);
        if ($validator->fails()) {
            $this->common->response(false, $validator->errors(), NULL, HTTP_BAD_REQUEST);
        }

        $user = (new User)->hasAccess($a['username']);

        if (is_null($user)) {
            $this->common->response(false, USER_NOT_FOUND, NULL, HTTP_BAD_REQUEST);
        } else {
            $authenticated = User::CheckPassword($a['password'], $user->password);
            if ($authenticated) {
                $access_token = $this->common->getAccessToken($a['username'], $a['password']);
                $this->common->response(true,  NULL,$access_token, HTTP_OK);
            } else {
                $this->common->response(false, PASSWORD_MISMATCH, NULL, HTTP_BAD_REQUEST);
            }
        }
    }
}
