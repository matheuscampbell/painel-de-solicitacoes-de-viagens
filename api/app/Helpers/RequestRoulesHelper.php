<?php

namespace App\Helpers;

class RequestRoulesHelper
{
    public static function getRoules($requestClass, string|bool $transformInArrayWithKey=false): array
    {
        $class = new $requestClass;
        if($transformInArrayWithKey){
            return self::transformRulesInArray($class->rules(), $transformInArrayWithKey);
        }
        return $class->rules();
    }

    public static function transformRulesInArray($rules, $arrayKey): array
    {
        $array = [];
        foreach ($rules as $key => $value) {
            $array[$arrayKey.$key] = $value;
        }
        return $array;
    }

    public static function getRequestRules(array $requestClasses = [], array $additionalRoules = []): array
    {
        $array = [];
        foreach ($requestClasses as $key => $value) {
            if(is_string($key))
                $array = array_merge($array, self::getRoules($value, $key));
            else
                $array = array_merge($array, self::getRoules($value));
        }
        $array = array_merge($array, $additionalRoules);
        return $array;
    }

}
