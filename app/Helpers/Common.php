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
        $locale = $locale == 'en' ? 'en' : 'fa';

        return $locale;
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

    public static function trimInput($key)
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

    public static function fileUploadAPI($idName, $allowedSize, $allowedFormat, $moveToFolder, $entity_type, $entity_id, $caption, $api_user_id = null)
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
            return FILE_UPLOADER_ERROR_GENERAL . ' ' . errorUploadFileStringMapping($error);
        } else {
            if ($size > $allowedSize) {
                return FILE_UPLOADER_ERROR_SIZE . ' ' . $allowedSize;
            }
            $allowedMimeTypes = processExtensionAndMime($allowedFormat);
            if (!in_array($type, $allowedMimeTypes)) {
                return FILE_UPLOADER_ERROR_FORMAT;
            } else {

                $exists = true;
                $extension = getExtension($name);
                while ($exists) {
                    $newfile = $entity_type != 16 ? generateFilename() . '.' . $extension : $name;
                    $file_path = $moveToFolder . $newfile;
                    $exists = \App\Models\File::existsByPath($file_path);
                }
                $file = $root . $file_path;
                $done = move_uploaded_file($temp, $file);
                {
                    $hash = hash('md5', $name);
                    $r = self::fileInsert($caption, $file_path, $api_user_id != null && $api_user_id != '' ? $api_user_id : Auth::user()->id, $entity_type, $entity_id, $name, $size, $extension, $hash);
                    if ($r >= 1) {
                        return [$file_path, $r, env('APP_URL')];
                    } else {
                        return FILE_UPLOADER_ERROR_DB_INSERTION;
                    }
                }
                return true;
            }
        }
    }

    public static function fileUpload($idName, $allowedSize, $allowedFormat, $moveToFolder, $entity_type, $entity_id, $caption, $filename = null)
    {
        $root = ''; //$_SERVER["DOCUMENT_ROOT"] . '/';
        $name = $_FILES[$idName]["name"];
        $type = $_FILES[$idName]["type"];
        $size = $_FILES[$idName]["size"];
        $temp = $_FILES[$idName]["tmp_name"];
        $error = $_FILES[$idName]["error"];
        $caption = $caption == "" ? $name : $caption;

        if ($error > 0) {
            return FILE_UPLOADER_ERROR_GENERAL . ' ' . errorUploadFileStringMapping($error);
        } else {
            if ($size > $allowedSize) {
                return FILE_UPLOADER_ERROR_SIZE . ' ' . $allowedSize;
            }
            $allowedMimeTypes = processExtensionAndMime($allowedFormat);
            if (!in_array($type, $allowedMimeTypes)) {
                return FILE_UPLOADER_ERROR_FORMAT;
            } else {
                if ($entity_type == 6) {
                    //upload epub on other server
                    $epub_upload_result = self::uploadEpub($name, $caption, $size, $entity_id, $temp);
                    return [$epub_upload_result, true];
                } else {
                    $exists = true;
                    $extension = getExtension($name);

                    if ($filename != null) {

                        $newfile = $filename . '.' . $extension;
                        $file_path = $moveToFolder . $newfile;
                    } else {

                        while ($exists) {
                            $newfile = generateFilename() . '.' . $extension;
                            $file_path = $moveToFolder . $newfile;
                            $exists = \App\Models\File::existsByPath($file_path);
                        }
                    }

                    $file = $root . $file_path;
                    $done = move_uploaded_file($temp, $file);
                    {
                        $hash = hash('md5', $name);
                        $r = self::fileInsert($caption, $file_path, Auth::user()->id, $entity_type, $entity_id, $name, $size, $extension, $hash);
                        if ($r >= 1) {
                            return [$r, $file_path];
                        } else {
                            return FILE_UPLOADER_ERROR_DB_INSERTION;
                        }
                    }
                    return true;
                }
                return true;
            }
        }
    }

    public static function fileInsert($caption, $path, $user_id, $entity_type, $entity_id, $name, $size, $extension, $hash)
    {
        return \App\Models\File::insert([
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

    public static function getHostRequest(): string
    {
        $host = request()->getHttpHost();
        if($host == '127.0.0.1:8000')
        {
            $host = '127.0.0.1:8001';
        }
        return $host;
    }

}
