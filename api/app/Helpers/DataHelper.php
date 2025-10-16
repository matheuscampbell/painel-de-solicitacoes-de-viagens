<?php

namespace App\Helpers;


class DataHelper
{
    public static function get($data, $default = null, $exceptonMessage = false )
    {
        if (!$data) {
            if ($exceptonMessage) throw new \Exception($exceptonMessage);
            return $default;
        }
        return $data;
    }
    public static function getItem( $data, $item, $default = null)
    {
        if(str_contains($item, '.')) {
            $item = explode('.', $item);
            foreach ($item as $key) {
                $data = $data[$key] ?? null;
            }
        } else {
            $data = $data[$item] ?? null;
        }
        if (empty($data) && !is_bool($data)) {
            if ($default instanceof \Exception) throw $default;
            return $default;
        }
        return $data;
    }

    public static function getItemEndJsonEncode( $data, $item, $default = null, $exceptonMessage = false )
    {
        $data = self::getItem($data, $item, array(), $exceptonMessage);
        return json_encode($data);
    }

    public static function getItemJson( $data, $item, $default = null, $exceptonMessage = false )
    {
        if(is_string($data)) $data = json_decode($data);
        $data = self::getItem($data, $item, array(), $exceptonMessage);
        return json_encode($data);
    }
}
