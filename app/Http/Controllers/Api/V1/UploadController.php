<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    protected $common;

    function __construct()
    {
        $this->common = new CommonController();
    }

    public function fileUpload(){
        $user = Auth::user();
        $inputs = \Illuminate\Support\Facades\Request::all();

        //check params
        $validator = Validator::make($inputs, RULES_FILE_UPLOAD);
        if ($validator->fails()) {
            $this->common->response(false, $validator->errors(), NULL, HTTP_BAD_REQUEST);
        }

        //check entity_type
        $allowed_entity_types = ALLOWED_ENTITY_TYPES;
        $entity_type = (int)$inputs['entity_tpe'];
        if (!in_array($entity_type, $allowed_entity_types)) {
            $this->common->response(false, CANNOT_UPLOAD_FOR_THIS_ENTITY, NULL, HTTP_BAD_REQUEST);
        }

        $allowed_size = ALLOWED_ENTITY_SIZE;

        //Determine the general condition
        switch ($entity_type) {
            case 1: //articles photo
                $types = UPLOAD_ARTICLE_PHOTO_FILE_TYPES;
                $allowedSize = MAX_UPLOAD_ARTICLE_PHOTO_FILE_SIZE;
                $folder = 'assets/articles/photo';
                $role_id = USERS_WHO_CAN_UPLOAD_ARTICLE_PHOTO;
                break;
            case 2: //articles video
                $types = UPLOAD_ARTICLE_VIDEO_FILE_TYPES;
                $allowedSize = MAX_UPLOAD_ARTICLE_VIDEO_FILE_SIZE;
                $folder = 'assets/articles/photo';
                $role_id = USERS_WHO_CAN_UPLOAD_ARTICLE_VIDEO;
                break;
            case 3: //articles pdf
                $types = UPLOAD_ARTICLE_PDF_FILE_TYPES;
                $allowedSize = MAX_UPLOAD_ARTICLE_PDF_FILE_SIZE;
                $folder = 'assets/articles/photo';
                $role_id = USERS_WHO_CAN_UPLOAD_PDF;
                break;
            default:
        }


    }
}
