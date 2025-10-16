<?php

namespace App\Enums;

class Enum
{

    public static function getValues()
    {
        $reflection = new \ReflectionClass(static::class);
        return array_values($reflection->getConstants());
    }
    //retorno array de strings
    public static function getKeys(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return array_keys($reflection->getConstants());
    }

    public static function fromKey($key)
    {
        $constants = static::getValues();
        $keys = self::getKeys();
        $index = array_search($key, $keys);

        if ($index !== false && isset($constants[$index])) {
            return $constants[$index];
        }

        return null;
    }
}