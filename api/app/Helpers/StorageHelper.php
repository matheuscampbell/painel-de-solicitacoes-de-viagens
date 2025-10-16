<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageHelper
{

    public const BASE_PATH = 'uploads/';
    public static function save($file, $content)
    {
        Storage::put($file, $content);
    }

    public static function saveBase64($content)
    {
        if(!$content) return null;
        list($type, $base64File) = explode(';', $content);
        list(, $extension) = explode('/', $type);
        list(, $base64File)      = explode(',', $base64File);

        $fileContent = base64_decode($base64File);

        $filename = self::uniqueName(). '.' . $extension;
        $path = self::BASE_PATH . $filename;

        Storage::put($path, $fileContent);
        return $filename;
    }



    public static function delete($file)
    {
        Storage::delete(self::BASE_PATH.$file);
    }

    public static function uniqueName($ext = false)
    {
       return Str::random(10).uniqid(more_entropy: true).'-'.date_format(new \DateTime(), 'Y-m-d-H-i-s').($ext ? '.'.$ext : '');
    }
}
