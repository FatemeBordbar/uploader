<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    public function login(){
        $parameters = ['username', 'password'];
        $a = Common::shaveInputAPI($parameters);

        $validator = Validator::make($a, RULES_LOGIN);
        if ($validator->fails()) {
            $this->common->response(false, $validator->errors(), NULL, HTTP_BAD_REQUEST);
        }

        $user = (new User)->hasAccess($a['username']);

    }
}
