<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    protected $common;

    function __construct()
    {
        $this->common = new CommonController();
    }

    public function fileUpload(){
        $parameters = ['file', 'entity_type','entity_type'];
        $a = Common::shaveInputAPI($parameters);

        $user = Auth::user();
        dd($user);

    }
}
