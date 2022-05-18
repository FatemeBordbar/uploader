<?php

namespace App\Http\Controllers\Api\V1;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CommonController extends Controller
{
    function response($status = true, $message = null, $data = null, $final_http_code = 200)
    {
        response()->json(['success' => $status, 'message' => $message, 'data' => $data], $final_http_code)->send();
        exit;
    }

    function getAccessToken($username, $password): array
    {
        $url = Common::getHostRequest() ."/". config()->get('uploader.oauth_path_get_token');
        $response = Http::asForm()->post($url, [
            'grant_type' => 'password',
            'client_id' => config()->get('uploader.oauth_client_id'),
            'client_secret' => config()->get('uploader.oauth_client_secret'),
            'username' => $username,
            'password' => $password,
            'scope' => '',
        ]);

        return $response->json();
    }
}
