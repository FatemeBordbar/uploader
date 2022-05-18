<?php

namespace App\Helpers;

use App\Events\ErrorEvent;
use App\Http\Controllers\Api\v1\StorageController;
use App\Http\Controllers\Api\v1\SubscriptionController;
use App\Models\Content;
use App\Models\File;
use App\Models\Invoice;
use App\Models\Membership;
use App\Models\SubscriptionPlan;
use App\Models\UserContent;
use Carbon\Carbon;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Models\SmsItem;
use App\Models\BookFavorite;
use App\Models\Category;
use App\Models\Reference;
use App\Models\ContentReference;
use App\Jobs\SendSingleSMSInstantly;
use Illuminate\Support\Facades\Storage;

use DateTime;

class Common
{

    public static function getAppLocale(): string
    {

        $locale = app()->getLocale();
        return $locale == 'en' ? 'en' : 'fa';
    }

    public static function shaveInputAPI($parameters): array
    {
        $o = Request::all();
        if ($o != null) {
            if ($parameters != null) {
                foreach ($o as $index => $item) {
                    if (!in_array($index, $parameters)) {
                        unset($o[$index]);
                    }
                }
            }
            return $o;
        }
        return [];
    }

    public static function trimInput($key): array
    {
        $o = Request::all();
        $r = [];
        unset($o['q'], $o['_token'], $o['id']);
        foreach ($o as $index => $item) {
            if ($item) {
                if (startsWith($index, $key . '[')) {
                    $temp = str_replace($key . '[', '', $index);
                    $temp = str_replace(']', '', $temp);
                    $r[] = $temp;
                }
            }
        }
        return $r;
    }

    public static function fileUploadAPI($idName, $allowedSize, $allowedFormat, $moveToFolder, $entity_type, $entity_id, $caption = null, $api_user_id): array|string
    {
        $root = "";
        if (!isset($_FILES[$idName])) {
            $idName = 'file';
        }

        $name = urldecode($_FILES[$idName]["name"]);
        $type = $_FILES[$idName]["type"];
        $size = $_FILES[$idName]["size"];
        $temp = $_FILES[$idName]["tmp_name"];
        $error = $_FILES[$idName]["error"];
        if ($error > 0) {
            return FILE_UPLOADER_ERROR_GENERAL . ' ' . self::errorUploadFileStringMapping($error);
        } else {
            if ($size > $allowedSize) {
                return FILE_UPLOADER_ERROR_SIZE . ' ' . $allowedSize;
            }

            $allowedMimeTypes = self::processExtensionAndMime($allowedFormat);
            if (!in_array($type, $allowedMimeTypes)) {
                return FILE_UPLOADER_ERROR_FORMAT;
            } else {

                $exists = true;
                $file_path = '';
                $extension = self::getExtension($name);
                while ($exists) {
                    $file_path = $moveToFolder . self::generateFilename() . '.' . $extension;
                    $exists = File::existsByPath($file_path);
                }
                $file = $root . $file_path;
                $done = move_uploaded_file($temp, $file);
                {
                    $hash = hash('md5', $name);
                    $file_id = self::fileInsert($caption, $file_path, $api_user_id, $entity_type, $entity_id, $name, $size, $extension, $hash);
                    if ($file_id >= 1) {
                        return ['file_path: ' =>$file_path, 'file_inserted_id: ' =>$file_id];
                    } else {
                        return FILE_UPLOADER_ERROR_DB_INSERTION;
                    }
                }
                return true;
            }
        }
    }

    public static function fileInsert($caption, $path, $user_id, $entity_type, $entity_id, $name, $size, $extension, $hash)
    {
        return File::insert([
            'caption' => $caption,
            'path' => $path,
            'user_id' => $user_id,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'original_name' => $name,
            'size' => $size,
            'extensions' => $extension,
            'hash' => $hash,
        ]);
    }

    public static function getHostRequestAuthApi(): string
    {
        $host = request()->getHttpHost();
        if($host == '127.0.0.1:8000')
        {
            $host = '127.0.0.1:8001';
        }
        return $host;
    }

    // Helpers Methods
    public static function processExtensionAndMime($in): array
    {
        if (strlen($in) <= 2) {
            return [];
        }
        $extensions = explode('|', $in);
        $returnee = [];
        foreach ($extensions as $extension) {
            if ($extension != '' && $extension !== null) {
                $returnee = array_merge($returnee, self::extensionToMimeType($extension));
            }
        }
        return $returnee;
    }

    public static function extensionToMimeType($in): array
    {
        switch ($in) {
            case 'jpg':
            case 'jpeg':
                return ['image/jpeg'];
            case 'bmp':
                return ['image/bmp'];
            case 'png':
                return ['image/png'];
            case 'txt':
                return ['text/plain'];
            case 'doc':
                return ['application/msword'];
            case 'docx':
                return ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            case 'pdf':
                return ['application/pdf'];
            case 'avi':
                return ['video/avi', 'application/x-troff-msvideo', 'video/msvideo', 'video/x-msvideo'];
            case '':
            default:
                throw new \Exception("Extension to Mime Failure: " . $in);
        }
        throw new \Exception("Extension to Mime Failure: " . $in);
    }

    public static function getExtension($file): array|string
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    public static function generateFilename(): string
    {
        $length = 20;
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::cryptoRandSecure(0, strlen($codeAlphabet))];
        }
        return $token;
    }

    public static function errorUploadFileStringMapping($error): string
    {
        return match ($error) {
            0 => 'UPLOAD_ERR_OK',
            1 => 'UPLOAD_ERR_INI_SIZE',
            2 => 'UPLOAD_ERR_FORM_SIZE',
            3 => 'UPLOAD_ERR_PARTIAL',
            4 => 'UPLOAD_ERR_NO_FILE',
            6 => 'UPLOAD_ERR_NO_TMP_DIR',
            7 => 'UPLOAD_ERR_CANT_WRITE',
            8 => 'UPLOAD_ERR_EXTENSION',
            default => 'UPLOAD_ERR_UNKNOWN',
        };
        return 'UPLOAD_ERR_UNKNOWN';
    }

    public static function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 0) {
            return $min;
        }
        $log = log($range, 2);
        $bytes = (int)($log / 8) + 1;
        $bits = (int)$log + 1;
        $filter = (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);
        return $min + $rnd;
    }
}
