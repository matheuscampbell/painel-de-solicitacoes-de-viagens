<?php

namespace App\Dtos;

use App\Http\Requests\GetCarrinhoRequest;

abstract class TinyDto
{
    public function __construct(array $data)
    {
        $this->create($data);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function create(array $request)
    {
        $allVars = get_class_vars(static::class);
        foreach ($allVars as $key => $value) {
            if (isset($request[$key])) {
                $this->$key = $request[$key];
            }
        }
    }

    public function toArray($skip = [], $only = [], array|bool $skipIfNull = [])
    {
        $allVars = get_class_vars(static::class);
        $array = [];
        foreach ($allVars as $key => $value) {
            if (in_array($key, $skip)) {
                continue;
            }
            if (!empty($only) && !in_array($key, $only)) {
                continue;
            }
            if (($skipIfNull === true || in_array($key, $skipIfNull)) && is_null($this->$key)) {
                continue;
            }
            $array[$key] = $this->$key;
        }
        return $array;
    }

}
